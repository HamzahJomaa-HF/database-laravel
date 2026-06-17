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

            // Check if financial record exists for this activity and user
            $existing = ActivityFinancial::where('activity_id', $this->activityId)
                ->where('user_id', $user->user_id)
                ->first();

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
                // Parse numeric values for financial fields (removed OMT cost fields)
                elseif (in_array($column, [
                    'amount', 'medicine_cost', 'assistance_cost_after_pharmacy_discount', 
                    'discount_percentage', 'operation_cost', 'medical_assistance', 
                    'residual_amount', 'covered_percentage', 'tuition_fees', 
                    'scholarship_percentage', 'student_count', 'patient_count'
                ])) {
                    $financialData[$column] = $this->parseNumeric($value);
                }
                // Parse integer values
                elseif (in_array($column, ['patient_count', 'student_count'])) {
                    $financialData[$column] = $this->parseInteger($value);
                }
                else {
                    $financialData[$column] = $value;
                }
            }
        }
        
        // For medical records, ensure medication_type is set
        if ($this->financialType === 'medical' && empty($financialData['medication_type']) && !empty($row['medication_type'])) {
            $financialData['medication_type'] = strtolower(trim($row['medication_type']));
        }
        
        // Add OMT contact fields to financial_data if they exist in the row
        if ($this->financialType === 'omt') {
            if (!empty($row['sender_name'])) {
                $financialData['sender_name'] = trim($row['sender_name']);
            }
            if (!empty($row['receiver_name'])) {
                $financialData['receiver_name'] = trim($row['receiver_name']);
            }
            if (!empty($row['collector_name'])) {
                $financialData['collector_name'] = trim($row['collector_name']);
            }
            if (!empty($row['omt_number'])) {
                $financialData['omt_number'] = trim($row['omt_number']);
            }
            if (!empty($row['sender_number'])) {
                $financialData['sender_number'] = trim($row['sender_number']);
            }
            if (!empty($row['correction_date'])) {
                $financialData['correction_date'] = $this->parseDate($row['correction_date']);
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

        // Try to find by person_id
        if (!empty($row['person_id'])) {
            $user = User::where('person_id', $row['person_id'])->first();
            if ($user) {
                Log::info("User found by person_id: {$row['person_id']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by istimara_id
        if (!empty($row['istimara_id'])) {
            $user = User::where('istimara_id', $row['istimara_id'])->first();
            if ($user) {
                Log::info("User found by istimara_id: {$row['istimara_id']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by email
        if (!empty($row['email'])) {
            $user = User::where('email', $row['email'])->first();
            if ($user) {
                Log::info("User found by email: {$row['email']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by identification_id
        if (!empty($row['identification_id'])) {
            $user = User::where('identification_id', $row['identification_id'])->first();
            if ($user) {
                Log::info("User found by identification_id: {$row['identification_id']}");
                $this->updateUser($user, $row);
                return $user;
            }
        }

        // Try to find by phone
        if (!empty($row['phone_number'])) {
            $user = User::where('phone_number', $row['phone_number'])->first();
            if ($user) {
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
            $scope = ucfirst(strtolower(trim($row['scope'])));
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

        $scope = 'National';
        if (!empty($row['scope'])) {
            $scope = ucfirst(strtolower(trim($row['scope'])));
            $allowed = ['International', 'Regional', 'National', 'Local'];
            if (!in_array($scope, $allowed)) {
                $scope = 'National';
            }
        }

        $gender = 'Not Specified';
        if (!empty($row['gender'])) {
            $gender = ucfirst(strtolower(trim($row['gender'])));
            $genderMap = ['m' => 'Male', 'male' => 'Male', 'f' => 'Female', 'female' => 'Female', 'other' => 'Other'];
            $gender = $genderMap[strtolower($gender)] ?? 'Not Specified';
        }

        $orgType1 = 'Private Sector';
        if (!empty($row['organization_type_1'])) {
            $orgType1 = ucwords(strtolower(trim($row['organization_type_1'])));
        }

        $status1 = !empty($row['status_1']) ? trim($row['status_1']) : 'Active';
        $address = !empty($row['address']) ? trim($row['address']) : 'Not Provided';
        $phoneNumber = !empty($row['phone_number']) ? trim($row['phone_number']) : 'Not Provided';
        $position1 = !empty($row['position_1']) ? trim($row['position_1']) : 'Not Specified';
        $organization1 = !empty($row['organization_1']) ? trim($row['organization_1']) : 'Not Specified';

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
        if (!empty($row['organization_type_2'])) $userData['organization_type_2'] = ucwords(strtolower(trim($row['organization_type_2'])));
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
            $formats = ['Y-m-d', 'm/d/Y', 'm/d/y', 'd/m/Y', 'd/m/y'];
            foreach ($formats as $format) {
                $parsed = Carbon::createFromFormat($format, trim($date));
                if ($parsed) return $parsed->format('Y-m-d');
            }
            return Carbon::parse(trim($date))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normalizePaymentStatus($status)
    {
        if (empty($status)) return 'pending';
        $status = strtolower(trim($status));
        $mapping = [
            'paid' => 'paid', 'pay' => 'paid',
            'pending' => 'pending', 'pend' => 'pending',
            'partial' => 'partial', 'part' => 'partial',
            'overdue' => 'overdue', 'over' => 'overdue', 'late' => 'overdue'
        ];
        return $mapping[$status] ?? 'pending';
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