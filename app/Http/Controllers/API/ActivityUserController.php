<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityUser;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivityUserController extends Controller
{
    /**
     * Import participants from 3 CSV files and attach them to a specific activity.
     */
    public function importCsvFiles($activityId)
    {
        // File paths
        $files = [
            'identification-passport' => "C:\\Users\\AyaAntar\\OneDrive - Hariri Foundation\\Documents\\identification-passport.csv",
            'registration-place'      => "C:\\Users\\AyaAntar\\OneDrive - Hariri Foundation\\Documents\\registrationNumber-place.csv",
            'dob-phone-fullname'      => "C:\\Users\\AyaAntar\\OneDrive - Hariri Foundation\\Documents\\dob-phone-fullname.csv",
        ];

        $usersData = [];
        $rowNumber = 0; // unique counter across all files

        // Helper function to generate a unique key for merging
        $makeKey = function($firstName, $lastName, $registerNumber = null, $passportNumber = null) use (&$rowNumber) {
            $rowNumber++;
            $key = strtolower(trim($firstName . '_' . $lastName));
            if (!empty($registerNumber)) $key .= '_' . $registerNumber;
            elseif (!empty($passportNumber)) $key .= '_' . $passportNumber;
            else $key .= '_' . $rowNumber;
            return $key;
        };

        ///////////////////////////////////////////////////////
        // 1️⃣ FILE 1 — identification-passport.csv
        ///////////////////////////////////////////////////////
        if (file_exists($files['identification-passport']) && ($handle = fopen($files['identification-passport'], 'r')) !== false) {
            fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_map('trim', $row);
                if (count(array_filter($row)) === 0) continue;

                $first = $row[2] ?? '';
                $last  = $row[3] ?? '';
                if ($first === '' && $last === '') continue;

                $key = $makeKey($first, $last, null, $row[1] ?? null);

                $usersData[$key] = array_merge($usersData[$key] ?? [], [
                    'identification_id' => $row[0] ?? null,
                    'passport_number'   => $row[1] ?? null,
                    'first_name'        => $first,
                    'last_name'         => $last,
                    'type'              => $row[4] ?? null,
                    'attended'          => $row[5] ?? null,
                    'notes'             => $row[6] ?? null,
                ]);
            }
            fclose($handle);
        }

        ///////////////////////////////////////////////////////
        // 2️⃣ FILE 2 — registration-place.csv
        ///////////////////////////////////////////////////////
        if (file_exists($files['registration-place']) && ($handle = fopen($files['registration-place'], 'r')) !== false) {
            fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_map('trim', $row);
                if (count(array_filter($row)) === 0) continue;

                $first = $row[4] ?? '';
                $last  = $row[6] ?? '';
                if ($first === '' && $last === '') continue;

                $key = $makeKey($first, $last, $row[0] ?? null, null);

                $usersData[$key] = array_merge($usersData[$key] ?? [], [
                    'register_number' => $row[0] ?? null,
                    'register_place'  => $row[1] ?? null,
                    'dob'             => $row[2] ?? null,
                    'phone_number'    => $row[3] ?? null,
                    'first_name'      => $first,
                    'middle_name'     => $row[5] ?? null,
                    'last_name'       => $last,
                    'type'            => $row[7] ?? null,
                    'attended'        => $row[8] ?? null,
                    'notes'           => $row[9] ?? null,
                ]);
            }
            fclose($handle);
        }

        ///////////////////////////////////////////////////////
        // 3️⃣ FILE 3 — dob-phone-fullname.csv
        ///////////////////////////////////////////////////////
        if (file_exists($files['dob-phone-fullname']) && ($handle = fopen($files['dob-phone-fullname'], 'r')) !== false) {
            fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_map('trim', $row);
                if (count(array_filter($row)) === 0) continue;

                $first = $row[2] ?? '';
                $last  = $row[4] ?? '';
                if ($first === '' && $last === '') continue;

                $key = $makeKey($first, $last);

                // Parse DOB safely
                $dob = $row[0] ?? null;
                if ($dob) {
                    try {
                        $dob = Carbon::parse($dob)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dob = null;
                    }
                }

                $usersData[$key] = array_merge($usersData[$key] ?? [], [
                    'dob'           => $dob,
                    'phone_number'  => $row[1] ?? null,
                    'first_name'    => $first,
                    'middle_name'   => $row[3] ?? null,
                    'last_name'     => $last,
                    'type'          => $row[5] ?? null,
                    'attended'      => $row[6] ?? null,
                    'notes'         => $row[7] ?? null,
                ]);
            }
            fclose($handle);
        }

        ///////////////////////////////////////////////////////
        // 4️⃣ Insert Users & ActivityUsers
        ///////////////////////////////////////////////////////
        $totalInserted = 0;

        foreach ($usersData as $user) {
            // Skip if first and last name missing
            if (empty($user['first_name']) && empty($user['last_name'])) continue;

            // Create or update User
            $userModel = User::updateOrCreate(
                [
                    'identification_id' => $user['identification_id'] ?? null,
                    'passport_number'   => $user['passport_number'] ?? null,
                    'register_number'   => $user['register_number'] ?? null,
                ],
                [
                    'first_name'     => $user['first_name'] ?? null,
                    'middle_name'    => $user['middle_name'] ?? null,
                    'last_name'      => $user['last_name'] ?? null,
                    'dob'            => $user['dob'] ?? null,
                    'phone_number'   => $user['phone_number'] ?? null,
                    'register_place' => $user['register_place'] ?? null,
                ]
            );

            // Insert ActivityUser (use create to avoid overwriting)
            ActivityUser::create([
                'activity_id' => $activityId,
                'user_id'     => $userModel->user_id,
                'type'        => $user['type'] ?? null,
                'attended'    => strtolower($user['attended'] ?? '') === 'yes',
                'notes'       => $user['notes'] ?? null,
            ]);

            $totalInserted++;
        }

        return response()->json([
            'message' => '✅ All CSV files imported successfully!',
            'total_imported' => $totalInserted
        ]);
    }
}
