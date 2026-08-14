<?php

namespace App\Imports;

use App\Models\ActivityFinancial;
use App\Models\User;
use App\Models\Nationality;
use App\Models\Diploma;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FinancialsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private $activityId;
    private $financialType;
    private $createNewUsers;
    private $results;

    public function __construct($activityId, $financialType)
    {
        $this->activityId = $activityId;
        $this->financialType = $financialType;
        $this->createNewUsers = true; // Default to true - allows creating new users
        $this->results = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'users_created' => 0,
            'users_updated' => 0,
            'users_found' => 0,
            'users_not_found' => 0,
            'medical_breakdown' => [
                'medicine' => 0,
                'hospital' => 0
            ],
            'errors' => []
        ];
    }

    /**
     * Set whether to create new users or only find existing ones
     */
    public function setCreateNewUsers($value)
    {
        $this->createNewUsers = $value;
    }

    public function model(array $row)
    {
        // Clean the row keys (remove BOM, fix hyphens)
        $cleanedRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = trim(str_replace("\xEF\xBB\xBF", '', $key));
            $cleanKey = str_replace('-', '_', $cleanKey);
            $cleanedRow[$cleanKey] = $value;
        }

        Log::info('Processing row for user: ' . ($cleanedRow['person_id'] ?? $cleanedRow['email'] ?? 'unknown'));

        DB::beginTransaction();

        try {
            // Parse common values
            $txDate = $this->parseDate($cleanedRow['tx_date'] ?? null);
            $paymentStatus = $this->normalizePaymentStatus($cleanedRow['payment_status'] ?? null);
            $amount = $this->parseNumeric($cleanedRow['amount'] ?? null);

            // Find or create user
            $user = $this->findOrCreateUser($cleanedRow);

            if (!$user) {
                DB::commit();
                return null;
            }
            
            $this->results['users_found']++;

            // Build financial_data based on type
            $financialData = $this->buildFinancialData($cleanedRow);

            // Track medical subtype breakdown
            if ($this->financialType === 'medical' && isset($financialData['medication_type'])) {
                $medicationType = $financialData['medication_type'];
                if ($medicationType === 'medicine') {
                    $this->results['medical_breakdown']['medicine']++;
                } elseif ($medicationType === 'hospital') {
                    $this->results['medical_breakdown']['hospital']++;
                }
            }

            // Check if financial record exists for this activity, user, and exact type.
            // For medical, match on medication_type so medicine and hospital records
            // are kept separate. Legacy records with no medication_type in JSONB are
            // also matched here so a re-import fixes them instead of creating duplicates.
            $existingQuery = ActivityFinancial::where('activity_id', $this->activityId)
                ->where('user_id', $user->user_id)
                ->where('financial_type', $this->financialType);

            if ($this->financialType === 'medical' && !empty($financialData['medication_type'])) {
                $existingQuery->where(function ($q) use ($financialData) {
                    $q->whereRaw(
                        "financial_data->>'medication_type' = ?",
                        [$financialData['medication_type']]
                    )->orWhereRaw("(financial_data->>'medication_type') IS NULL");
                });
            }

            $existing = $existingQuery->first();

            if ($existing) {
                // Check if the data is exactly the same
                $existingFinancialData = $existing->financial_data ?? [];
                $isSameData = (
                    $existing->amount == $amount &&
                    $existing->payment_status == $paymentStatus &&
                    $existing->tx_date == $txDate &&
                    json_encode($existingFinancialData) == json_encode($financialData) &&
                    $existing->notes == ($cleanedRow['notes'] ?? null)
                );
                
                if ($isSameData) {
                    $this->results['skipped']++;
                    Log::info("Skipped - identical record for user: {$user->user_id}");
                    DB::commit();
                    return null;
                }
                
                // Update existing record - merge financial data
                $mergedFinancialData = array_merge($existingFinancialData, $financialData);

                $existing->update([
                    'financial_type' => $this->financialType,
                    'amount' => $amount ?? $existing->amount,
                    'payment_status' => $paymentStatus,
                    'tx_date' => $txDate ?? $existing->tx_date,
                    'financial_data' => $mergedFinancialData,
                    'notes' => $cleanedRow['notes'] ?? $existing->notes,
                ]);
                $this->results['updated']++;
                Log::info("Updated financial record for user: {$user->user_id}");
            } else {
                // Create new record
                ActivityFinancial::create([
                    'activity_financial_id' => (string) Str::uuid(),
                    'activity_id' => $this->activityId,
                    'user_id' => $user->user_id,
                    'cop_id' => null,
                    'financial_type' => $this->financialType,
                    'amount' => $amount,
                    'payment_status' => $paymentStatus,
                    'tx_date' => $txDate,
                    'financial_data' => $financialData,
                    'external_id' => (string) Str::uuid(),
                    'notes' => $cleanedRow['notes'] ?? null,
                ]);
                $this->results['imported']++;
                Log::info("Created financial record for user: {$user->user_id}");
            }

            DB::commit();
            return null;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Row processing failed: ' . $e->getMessage());
            $this->results['errors'][] = $e->getMessage();
            return null;
        }
    }

    /**
     * Build financial_data array - ALL fields go here dynamically
     */
    private function buildFinancialData($row)
    {
        // Define common columns that should NOT go into financial_data
        $commonColumns = [
            'person_id', 'istimara_id', 'prefix', 'is_high_profile', 'scope',
            'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
            'organization_type_1', 'status_1', 'address', 'phone_number', 'sector',
            'middle_name', 'mother_name', 'dob', 'office_phone', 'extension_number',
            'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2',
            'status_2', 'identification_id', 'register_number', 'marital_status',
            'employment_status', 'passport_number', 'register_place', 'type',
            'diploma_name', 'nationality_name', 'amount', 'payment_status', 'tx_date', 'notes'
        ];
        
        // Get all columns from the row
        $allColumns = array_keys($row);
        
        // Build financial_data from all columns NOT in commonColumns
        $financialData = [];
        
        foreach ($allColumns as $column) {
            if (!in_array($column, $commonColumns) && !empty($row[$column])) {
                $value = $row[$column];
                
                // Special handling for date fields
                if (str_contains($column, 'date') || $column === 'correction_date') {
                    $financialData[$column] = $this->parseDate($value);
                } 
                // Parse numeric values for financial fields
                elseif (in_array($column, [
                    'amount', 'medicine_cost', 'assistance_cost_after_pharmacy_discount',
                    'discount_percentage', 'operation_cost', 'medical_assistance',
                    'residual_amount', 'covered_percentage', 'tuition_fees',
                    'scholarship_percentage', 'student_count', 'patient_count',
                    'books_supplies', 'living_allowance', 'registration_fees',
                ])) {
                    $financialData[$column] = $this->parseNumeric($value);
                }
                else {
                    $financialData[$column] = $value;
                }
            }
        }
        
        // For medical records, resolve medication_type.
        // The generic loop may have picked up any string from the CSV (including drug names
        // like "Painkiller"), so we MUST validate the value is one of the two valid subtypes.
        // If it is not, fall through to auto-detect from field presence.
        if ($this->financialType === 'medical') {
            $validSubtypes = ['hospital', 'medicine'];

            // Grab whatever was captured (from generic loop or raw row)
            $rawType = strtolower(trim($financialData['medication_type'] ?? $row['medication_type'] ?? ''));

            if (in_array($rawType, $validSubtypes)) {
                $financialData['medication_type'] = $rawType;
            } else {
                // Value is a drug name, blank, or otherwise invalid — auto-detect instead
                unset($financialData['medication_type']);

                $hospitalSignals = ['operation_type', 'operation_cost', 'medical_assistance',
                                    'residual_amount', 'covered_percentage', 'other_assistance'];
                $medicineSignals = ['disease_type', 'medicine_cost', 'invoice_number',
                                    'assistance_cost_after_pharmacy_discount', 'discount_percentage'];

                $hasHospital = false;
                foreach ($hospitalSignals as $f) {
                    if (!empty($row[$f])) { $hasHospital = true; break; }
                }

                $hasMedicine = false;
                foreach ($medicineSignals as $f) {
                    if (!empty($row[$f])) { $hasMedicine = true; break; }
                }

                if ($hasHospital && !$hasMedicine) {
                    $financialData['medication_type'] = 'hospital';
                } elseif ($hasMedicine) {
                    $financialData['medication_type'] = 'medicine';
                }
                // if neither signals are present, leave medication_type unset
            }
        }
        
        Log::info('Built financial_data: ' . json_encode($financialData));
        
        return $financialData;
    }

    private function findOrCreateUser($row)
    {
        Log::info('Attempting to find/create user with data:', [
            'person_id' => $row['person_id'] ?? null,
            'istimara_id' => $row['istimara_id'] ?? null,
            'email' => $row['email'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null
        ]);

        // Primary match: first_name + middle_name + last_name + dob + phone_number,
        // all together in a single comparison. All five fields must be present in the
        // row for this match to run — if any is missing, skip it (treated as no-match)
        // rather than risk merging two different people on a partial comparison.
        if (
            !empty($row['first_name']) && !empty($row['middle_name']) && !empty($row['last_name']) &&
            !empty($row['dob']) && !empty($row['phone_number'])
        ) {
            $parsedDob = $this->parseDate($row['dob']);

            if ($parsedDob) {
                $user = User::where('first_name', trim($row['first_name']))
                    ->where('middle_name', trim($row['middle_name']))
                    ->where('last_name', trim($row['last_name']))
                    ->where('phone_number', trim($row['phone_number']))
                    ->whereDate('dob', $parsedDob)
                    ->first();

                if ($user) {
                    Log::info("User found by first_name + middle_name + last_name + dob + phone_number match", [
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'],
                        'last_name' => $row['last_name'],
                        'dob' => $parsedDob,
                        'phone_number' => $row['phone_number'],
                        'matched_user_id' => $user->user_id,
                    ]);
                    $this->updateUser($user, $row);
                    return $user;
                }
            }
        }

        // Try to find by person_id
        if (!empty($row['person_id'])) {
            $user = User::where('person_id', $row['person_id'])->first();
            if ($user) {
                Log::info("User found by person_id: {$row['person_id']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by email — also require last_name (and first_name, if present) to
        // match. Email alone isn't guaranteed unique to one person in this dataset, so a
        // bare email match risks attaching the record to an unrelated user.
        if (!empty($row['email'])) {
            $user = User::where('email', $row['email'])->first();
            if ($user && $this->nameMatches($user, $row)) {
                Log::info("User found by email: {$row['email']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by identification_id — same name-match guard as above.
        if (!empty($row['identification_id'])) {
            $user = User::where('identification_id', $row['identification_id'])->first();
            if ($user && $this->nameMatches($user, $row)) {
                Log::info("User found by identification_id: {$row['identification_id']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by phone — same name-match guard as above. Phone numbers can be
        // shared between family members or reused across records, so a phone match alone
        // does not prove it is the same person (this caused wrong-user assignment in
        // production: a row was matched to an unrelated existing user purely by phone).
        if (!empty($row['phone_number'])) {
            $user = User::where('phone_number', $row['phone_number'])->first();
            if ($user && $this->nameMatches($user, $row)) {
                Log::info("User found by phone: {$row['phone_number']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Check if we are allowed to create new users
        if (!$this->createNewUsers) {
            Log::info('User not found and createNewUsers is disabled');
            $this->results['users_not_found']++;
            return null;
        }

        // Create new user
        Log::info('No existing user found, creating new user...');
        try {
            $userData = $this->prepareUserData($row);
            Log::info('User data prepared:', $userData);
            
            $user = User::create($userData);
            $this->results['users_created']++;
            Log::info("User created successfully: {$user->user_id}");

            // Handle nationality
            if (!empty($row['nationality_name'])) {
                $nationality = Nationality::firstOrCreate(['name' => trim($row['nationality_name'])]);
                DB::table('users_nationality')->insert([
                    'user_id' => $user->user_id,
                    'nationality_id' => $nationality->nationality_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Nationality assigned: {$row['nationality_name']}");
            }

            // Handle diploma
            if (!empty($row['diploma_name'])) {
                $diploma = Diploma::firstOrCreate([
                    'diploma_name' => trim($row['diploma_name']),
                ]);
                DB::table('users_diploma')->insert([
                    'user_id' => $user->user_id,
                    'diploma_id' => $diploma->diploma_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Diploma assigned: {$row['diploma_name']}");
            }

            return $user;
            
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            Log::error('User data that caused error: ' . json_encode($userData ?? []));
            throw new \Exception('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Verifies a candidate match's name lines up with the CSV row before trusting a
     * single-field lookup (phone/email/identification_id) as proof it's the same person —
     * those fields aren't guaranteed unique to one person in this dataset. Requires
     * last_name to match, and first_name too if the row provides one.
     */
    private function nameMatches(User $user, array $row): bool
    {
        if (empty($row['last_name'])) {
            return false;
        }

        if (strcasecmp(trim((string) $user->last_name), trim($row['last_name'])) !== 0) {
            return false;
        }

        if (!empty($row['first_name']) && strcasecmp(trim((string) $user->first_name), trim($row['first_name'])) !== 0) {
            return false;
        }

        return true;
    }

    private function updateUser($user, $row)
    {
        $updated = false;

        $fieldsToUpdate = [
            'first_name', 'last_name', 'middle_name', 'mother_name', 'email',
            'phone_number', 'office_phone', 'home_phone', 'address', 'gender',
            'position_1', 'position_2', 'organization_1', 'organization_2',
            'organization_type_1', 'organization_type_2', 'status_1', 'status_2',
            'sector', 'identification_id', 'passport_number', 'register_number',
            'register_place', 'marital_status', 'employment_status', 'prefix',
            'person_id', 'istimara_id'
        ];

        foreach ($fieldsToUpdate as $field) {
            if (!empty($row[$field]) && (empty($user->$field) || $user->$field === 'Not Specified')) {
                $user->$field = trim($row[$field]);
                $updated = true;
            }
        }

        // Handle is_high_profile
        if (isset($row['is_high_profile'])) {
            $isHigh = $this->normalizeBoolean($row['is_high_profile']);
            if ($user->is_high_profile != $isHigh) {
                $user->is_high_profile = $isHigh;
                $updated = true;
            }
        }

        // Handle scope
        if (!empty($row['scope'])) {
            $scope = trim($row['scope']);
            if ($user->scope !== $scope) {
                $user->scope = $scope;
                $updated = true;
            }
        }

        // Handle dob
        if (!empty($row['dob']) && empty($user->dob)) {
            $user->dob = $this->parseDate($row['dob']);
            $updated = true;
        }

        if ($updated) {
            $user->save();
            $this->results['users_updated']++;
            Log::info("User updated: {$user->user_id}");
        }

        return $updated;
    }

    private function prepareUserData($row)
    {
        $firstName = trim($row['first_name'] ?? '');
        $lastName = trim($row['last_name'] ?? '');

        Log::info('Preparing user data', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'row_keys' => array_keys($row)
        ]);

        if (empty($firstName)) {
            throw new \Exception("first_name is required. Available columns: " . implode(', ', array_keys($row)));
        }
        if (empty($lastName)) {
            throw new \Exception("last_name is required. Available columns: " . implode(', ', array_keys($row)));
        }

        $isHighProfile = $this->normalizeBoolean($row['is_high_profile'] ?? false);

        // Store exactly what the Excel cell contains (trimmed). These columns are
        // NOT NULL in the DB with no default, so a blank cell becomes an empty
        // string rather than a fabricated placeholder like "Not Specified".
        $scope = trim($row['scope'] ?? '');
        $gender = trim($row['gender'] ?? '');
        $orgType1 = trim($row['organization_type_1'] ?? '');
        $status1 = trim($row['status_1'] ?? '');
        $address = trim($row['address'] ?? '');
        $phoneNumber = trim($row['phone_number'] ?? '');
        $position1 = trim($row['position_1'] ?? '');
        $organization1 = trim($row['organization_1'] ?? '');

        $userData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'scope' => $scope,
            'is_high_profile' => $isHighProfile,
            'gender' => $gender,
            'position_1' => $position1,
            'organization_1' => $organization1,
            'organization_type_1' => $orgType1,
            'status_1' => $status1,
            'address' => $address,
            'phone_number' => $phoneNumber,
        ];

        // Add optional fields
        if (!empty($row['middle_name'])) $userData['middle_name'] = trim($row['middle_name']);
        if (!empty($row['mother_name'])) $userData['mother_name'] = trim($row['mother_name']);
        if (!empty($row['email'])) $userData['email'] = strtolower(trim($row['email']));
        if (!empty($row['dob'])) $userData['dob'] = $this->parseDate($row['dob']);
        if (!empty($row['office_phone'])) $userData['office_phone'] = trim($row['office_phone']);
        if (!empty($row['extension_number'])) $userData['extension_number'] = trim($row['extension_number']);
        if (!empty($row['home_phone'])) $userData['home_phone'] = trim($row['home_phone']);
        if (!empty($row['position_2'])) $userData['position_2'] = trim($row['position_2']);
        if (!empty($row['organization_2'])) $userData['organization_2'] = trim($row['organization_2']);
        if (!empty($row['organization_type_2'])) $userData['organization_type_2'] = trim($row['organization_type_2']);
        if (!empty($row['status_2'])) $userData['status_2'] = trim($row['status_2']);
        if (!empty($row['sector'])) $userData['sector'] = trim($row['sector']);
        if (!empty($row['identification_id'])) $userData['identification_id'] = trim($row['identification_id']);
        if (!empty($row['passport_number'])) $userData['passport_number'] = trim($row['passport_number']);
        if (!empty($row['register_number'])) $userData['register_number'] = trim($row['register_number']);
        if (!empty($row['register_place'])) $userData['register_place'] = trim($row['register_place']);
        if (!empty($row['marital_status'])) $userData['marital_status'] = trim($row['marital_status']);
        if (!empty($row['employment_status'])) $userData['employment_status'] = trim($row['employment_status']);
        if (!empty($row['prefix'])) $userData['prefix'] = trim($row['prefix']);
        if (!empty($row['person_id'])) $userData['person_id'] = trim($row['person_id']);
        if (!empty($row['istimara_id'])) $userData['istimara_id'] = trim($row['istimara_id']);

        return $userData;
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;

        try {
            // Excel stores date-formatted cells as a serial day-count number
            // (e.g. 45312), not a string — convert those directly.
            if (is_numeric($date)) {
                return Carbon::create(1899, 12, 30)->addDays((int) $date)->format('Y-m-d');
            }

            $date = trim($date);

            // ISO format — unambiguous, validated so an impossible date (e.g. month 13)
            // never gets silently accepted.
            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
                return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
            }

            // Slash/dash separated numeric dates. d/m/Y (day-first, the regional
            // convention here) is tried before the US m/d/Y convention, and both are
            // validated with checkdate() — Carbon's createFromFormat() does NOT reject
            // an out-of-range guess like month=15, it silently overflows into the wrong
            // date instead of failing, so we can't rely on it to disambiguate safely.
            if (preg_match('#^(\d{1,2})[/\-](\d{1,2})[/\-](\d{2,4})$#', $date, $m)) {
                [$a, $b, $y] = [(int) $m[1], (int) $m[2], (int) $m[3]];
                if ($y < 100) $y += $y < 70 ? 2000 : 1900;

                if (checkdate($b, $a, $y)) {
                    return sprintf('%04d-%02d-%02d', $y, $b, $a); // d/m/Y
                }
                if (checkdate($a, $b, $y)) {
                    return sprintf('%04d-%02d-%02d', $y, $a, $b); // fallback: m/d/Y
                }
            }

            // Anything else (e.g. "January 15, 2024") — let Carbon's free-form parser try.
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("[IMPORT-DATE-UNPARSEABLE] Could not parse tx_date value: '{$date}'");
            return null;
        }
    }

    private function normalizePaymentStatus($status)
    {
        if (empty($status)) return 'pending';
        $status = strtolower(trim($status));
        $mapping = [
            'paid' => 'paid', 'pay' => 'paid', 'complete' => 'paid', 'completed' => 'paid',
            'pending' => 'pending', 'pend' => 'pending', 'unpaid' => 'pending', 'not paid' => 'pending',
            'partial' => 'partial', 'part' => 'partial', 'partially paid' => 'partial',
            'overdue' => 'overdue', 'over' => 'overdue', 'late' => 'overdue',
        ];

        if (isset($mapping[$status])) {
            return $mapping[$status];
        }

        // Value doesn't match a known synonym — store exactly what the cell contains
        // (the column is a free-text string, not a DB enum) instead of silently
        // relabeling it as 'pending', which would misrepresent the imported data.
        Log::warning("[IMPORT-PAYMENT-STATUS-UNRECOGNIZED] Using raw cell value as-is: '{$status}'");
        return $status;
    }

    private function normalizeBoolean($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'high', 'y']);
    }

    private function parseNumeric($value)
    {
        if ($value === null || $value === '') return null;
        $value = preg_replace('/[^0-9.-]/', '', $value);
        return is_numeric($value) ? (float) $value : null;
    }

    private function parseInteger($value)
    {
        if ($value === null || $value === '') return null;
        $value = preg_replace('/[^0-9-]/', '', $value);
        return is_numeric($value) ? (int) $value : null;
    }

    public function getResults()
    {
        return $this->results;
    }
}