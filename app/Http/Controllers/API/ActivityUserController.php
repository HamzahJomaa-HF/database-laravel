<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityUser;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="ActivityUsers",
 *     description="API Endpoints for managing Activity Users"
 * )
 */
class ActivityUserController extends Controller
{
    /**
     * List all ActivityUser records
     * 
     * @OA\Get(
     *     path="/api/activity-users",
     *     summary="List all ActivityUser records",
     *     tags={"ActivityUsers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of activity users",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $activityUsers = ActivityUser::all();
        return response()->json([
            'success' => true,
            'data' => $activityUsers
        ]);
    }

    /**
     * Create a new ActivityUser record (without file)
     *
     * @OA\Post(
     *     path="/api/activity-users",
     *     summary="Create a new ActivityUser record",
     *     tags={"ActivityUsers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string"),
     *             @OA\Property(property="activity_id", type="string"),
     *             @OA\Property(property="is_lead", type="boolean"),
     *             @OA\Property(property="invited", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'string',
            'activity_id' => 'string',
            'is_lead' => 'boolean',
            'invited' => 'boolean'
        ]);

        $activityUser = ActivityUser::create([
            'activity_user_id' => Str::uuid(),
            'user_id' => $request->user_id ?? $this->createOrFindUser($request->all())->user_id,
            'activity_id' => $request->activity_id ?? $this->createOrFindActivity($request->all())->activity_id,
            'is_lead' => $request->is_lead ?? false,
            'invited' => $request->invited ?? false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $activityUser
        ], 201);
    }

    /**
     * Upload a file (without specifying an ActivityUser ID)
     *
     * @OA\Post(
     *     path="/api/activity-users/upload",
     *     summary="Upload a file",
     *     tags={"ActivityUsers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary", description="File to upload"),
     *                 @OA\Property(property="activity_id", type="string", description="UUID of the activity (optional)"),
     *                 @OA\Property(property="activity_title", type="string", description="Title for new activity (if activity_id not provided)"),
     *                 @OA\Property(property="activity_type", type="string", description="Type for new activity")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="summary", type="object"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="activity_id", type="string")
     *         )
     *     )
     * )
     */
    public function uploadFileWithoutId(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'activity_id' => 'nullable|uuid|exists:activities,activity_id',
            'activity_title' => 'nullable|string',
            'activity_type' => 'nullable|string'
        ]);

        // Find or create activity
        $activity = null;
        if ($request->activity_id) {
            $activity = Activity::where('activity_id', $request->activity_id)->first();
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found'
                ], 404);
            }
        } else {
            // Create new activity if not provided
            $activityTitle = $request->activity_title ?? 'Imported Activity ' . date('Y-m-d H:i:s');
            $activity = Activity::create([
                'activity_id' => Str::uuid(),
                'external_id' => 'ACT_' . date('Y_m_d_His'),
                'activity_title' => $activityTitle,
                'activity_type' => $request->activity_type ?? 'imported',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
            ]);
            Log::info("Created new activity: " . $activity->activity_id);
        }

        $file = $request->file('file');
        $createdCount = 0;
        $skippedCount = 0;
        $errors = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Read and clean header
            $header = fgetcsv($handle);
            $header = array_map(function($col) {
                return trim(str_replace("\xEF\xBB\xBF", '', $col));
            }, $header);

            $lineNumber = 1;
            $errors[] = "CSV Headers: " . implode(', ', $header);

            // Detect file type for user creation logic
            $fileType = $this->detectCsvType($header);
            $errors[] = "Detected file type for user creation: " . $fileType;

            // Get the latest external_id to avoid duplicates
            $latestExternalId = ActivityUser::where('external_id', 'like', 'AU_' . date('Y_m_d') . '_%')
                ->orderBy('external_id', 'desc')
                ->value('external_id');
            
            $nextIdNumber = 1;
            if ($latestExternalId) {
                $parts = explode('_', $latestExternalId);
                $lastNumber = intval(end($parts));
                $nextIdNumber = $lastNumber + 1;
            }

            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;
                
                if (empty(array_filter($row))) {
                    $skippedCount++;
                    continue;
                }

                try {
                    if (count($row) !== count($header)) {
                        $errors[] = "Line {$lineNumber}: Column count mismatch";
                        $skippedCount++;
                        continue;
                    }

                    $data = array_combine($header, $row);
                    $data = array_map('trim', $data);

                    Log::info("Processing line {$lineNumber}: " . json_encode($data));

                    // Use database transaction to ensure data consistency
                    DB::transaction(function () use ($data, $fileType, $lineNumber, &$createdCount, &$errors, $activity, $header, &$nextIdNumber) {
                        // Find or create user
                        $user = $this->findOrCreateUserUniversal($data, $fileType, $lineNumber, $errors);
                        
                        if (!$user || empty($user->user_id)) {
                            throw new \Exception("Failed to find or create user - no valid user_id");
                        }

                        // Check if ActivityUser already exists
                        $existingActivityUser = ActivityUser::where('user_id', $user->user_id)
                            ->where('activity_id', $activity->activity_id)
                            ->first();

                        if ($existingActivityUser) {
                            throw new \Exception("User already associated with this activity");
                        }

                        // Get the type from CSV data or use default
                        $activityUserType = $data['type'] ?? 'beneficiary'; // Default to 'beneficiary' if not provided
                        
                        // Validate the type
                        $validTypes = ['stakeholder', 'beneficiary', 'participant', 'volunteer', 'staff'];
                        if (!in_array(strtolower($activityUserType), $validTypes)) {
                            $activityUserType = 'beneficiary'; // Fallback to beneficiary if invalid
                        }

                        // Generate unique external_id
                        $externalId = 'AU_' . date('Y_m_d') . '_' . str_pad($nextIdNumber, 3, '0', STR_PAD_LEFT);
                        $nextIdNumber++;

                        // Create ActivityUser record
                        ActivityUser::create([
                            'activity_user_id' => Str::uuid(),
                            'user_id' => $user->user_id,
                            'activity_id' => $activity->activity_id,
                            'external_id' => $externalId,
                            'type' => $activityUserType, // Use the type from CSV data
                            'is_lead' => false,
                            'invited' => false,
                            'attended' => isset($data['attended']) ? filter_var($data['attended'], FILTER_VALIDATE_BOOLEAN) : false,
                        ]);

                        $createdCount++;
                        Log::info("Successfully created ActivityUser for user: " . $user->user_id . " with type: " . $activityUserType . " and external_id: " . $externalId);
                    });

                } catch (\Exception $e) {
                    $skippedCount++;
                    $errorMsg = "Line {$lineNumber}: Error - " . $e->getMessage();
                    $errors[] = $errorMsg;
                    Log::error($errorMsg);
                    Log::error("Data: " . json_encode($data ?? []));
                }
            }

            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'message' => 'CSV file processed successfully',
            'activity_id' => $activity->activity_id,
            'activity_title' => $activity->activity_title,
            'summary' => [
                'created' => $createdCount,
                'skipped' => $skippedCount,
                'total_processed' => $createdCount + $skippedCount
            ],
            'errors' => $errors
        ]);
    }

    /**
     * Create or find user based on request data
     */
    private function createOrFindUser($data)
    {
        // If user_id is provided, find the user
        if (!empty($data['user_id'])) {
            $user = User::where('user_id', $data['user_id'])->first();
            if ($user) {
                return $user;
            }
        }

        // Otherwise create a new user with minimal data
        return User::create([
            'user_id' => Str::uuid(),
            'first_name' => $data['first_name'] ?? 'Unknown',
            'last_name' => $data['last_name'] ?? 'User',
            'middle_name' => $data['middle_name'] ?? '',
            'phone_number' => $data['phone_number'] ?? '',
            'identification_id' => $data['identification_id'] ?? '',
            'passport_number' => $data['passport_number'] ?? '',
            'dob' => !empty($data['dob']) ? $this->parseDate($data['dob']) : now()->format('Y-m-d'),
        ]);
    }

    /**
     * Create or find activity based on request data
     */
    private function createOrFindActivity($data)
    {
        // If activity_id is provided, find the activity
        if (!empty($data['activity_id'])) {
            $activity = Activity::where('activity_id', $data['activity_id'])->first();
            if ($activity) {
                return $activity;
            }
        }

        // Otherwise create a new activity
        return Activity::create([
            'activity_id' => Str::uuid(),
            'external_id' => 'ACT_' . date('Y_m_d_His'),
            'activity_title' => $data['activity_title'] ?? 'New Activity ' . date('Y-m-d H:i:s'),
            'activity_type' => $data['activity_type'] ?? 'general',
            'start_date' => !empty($data['start_date']) ? $this->parseDate($data['start_date']) : now()->format('Y-m-d'),
            'end_date' => !empty($data['end_date']) ? $this->parseDate($data['end_date']) : now()->addDays(30)->format('Y-m-d'),
        ]);
    }

    /**
     * Universal method to find or create user based on CSV type
     */
    private function findOrCreateUserUniversal($data, $fileType, $lineNumber, &$errors)
    {
        try {
            switch ($fileType) {
                case 'phone_type':
                    return $this->findOrCreateUserByPhone($data, $lineNumber, $errors);
                case 'identification_type':
                    return $this->findOrCreateUserByIdentification($data, $lineNumber, $errors);
                case 'register_type':
                    return $this->findOrCreateUserByRegister($data, $lineNumber, $errors);
                default:
                    // For unknown types, try phone type as fallback if phone number exists
                    if (!empty($data['phone_number']) && !empty($data['first_name']) && !empty($data['last_name'])) {
                        return $this->findOrCreateUserByPhone($data, $lineNumber, $errors);
                    }
                    throw new \Exception("User creation requires either identification_id, passport_number, or basic personal info with phone number");
            }
        } catch (\Exception $e) {
            $errors[] = "Line {$lineNumber}: User creation failed - " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Find or create user by phone number
     */
    private function findOrCreateUserByPhone($data, $lineNumber, &$errors)
    {
        // Check for minimum required fields
        $required = ['phone_number', 'first_name', 'last_name'];
        $this->validateRequiredFields($data, $required, $lineNumber);

        // Try to find existing user by phone number
        $user = User::where('phone_number', $data['phone_number'])->first();

        if (!$user) {
            $userData = [
                'user_id' => Str::uuid(),
                'dob' => !empty($data['dob']) ? $this->parseDate($data['dob']) : now()->format('Y-m-d'),
                'phone_number' => $data['phone_number'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => !empty($data['middle_name']) ? $data['middle_name'] : '',
                'identification_id' => '',
                'passport_number' => '',
            ];

            $user = User::create($userData);
            Log::info("Created new user by phone: " . $user->user_id);
        } else {
            Log::info("Found existing user by phone: " . $user->user_id);
        }

        return $user;
    }

    /**
     * Find or create user by identification
     */
    private function findOrCreateUserByIdentification($data, $lineNumber, &$errors)
    {
        if (!empty($data['identification_id'])) {
            $user = User::where('identification_id', $data['identification_id'])->first();
            if ($user) {
                Log::info("Found existing user by identification_id: " . $user->user_id);
                return $user;
            }
        }

        if (!empty($data['passport_number'])) {
            $user = User::where('passport_number', $data['passport_number'])->first();
            if ($user) {
                Log::info("Found existing user by passport_number: " . $user->user_id);
                return $user;
            }
        }

        // Create new user if not found
        $userData = [
            'user_id' => Str::uuid(),
            'identification_id' => $data['identification_id'] ?? '',
            'passport_number' => $data['passport_number'] ?? '',
            'first_name' => $data['first_name'] ?? 'N/A',
            'last_name' => $data['last_name'] ?? 'N/A',
            'middle_name' => $data['middle_name'] ?? '',
            'dob' => !empty($data['dob']) ? $this->parseDate($data['dob']) : now()->format('Y-m-d'),
            'phone_number' => $data['phone_number'] ?? '',
        ];

        $user = User::create($userData);
        Log::info("Created new user by identification: " . $user->user_id);
        return $user;
    }

    /**
     * Find or create user by register number
     */
    private function findOrCreateUserByRegister($data, $lineNumber, &$errors)
    {
        $required = ['register_number', 'register_place', 'first_name', 'last_name'];
        $this->validateRequiredFields($data, $required, $lineNumber);

        // Try to find existing user by register number and place
        $user = User::where('register_number', $data['register_number'])
                    ->where('register_place', $data['register_place'])
                    ->first();

        if (!$user) {
            $userData = [
                'user_id' => Str::uuid(),
                'register_number' => $data['register_number'],
                'register_place' => $data['register_place'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => !empty($data['middle_name']) ? $data['middle_name'] : '',
                'dob' => !empty($data['dob']) ? $this->parseDate($data['dob']) : now()->format('Y-m-d'),
                'phone_number' => $data['phone_number'] ?? '',
                'identification_id' => '',
                'passport_number' => '',
            ];

            $user = User::create($userData);
            Log::info("Created new user by register: " . $user->user_id);
        } else {
            Log::info("Found existing user by register: " . $user->user_id);
        }

        return $user;
    }

    /**
     * Validate required fields
     */
    private function validateRequiredFields($data, $required, $lineNumber)
    {
        $missing = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new \Exception("Missing required fields: " . implode(', ', $missing));
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now()->format('Y-m-d');
        }

        try {
            // Remove any unwanted characters and trim
            $dateString = trim($dateString);
            
            // Try different date formats
            $formats = ['m/d/Y', 'Y-m-d', 'd/m/Y', 'Y/m/d', 'm-d-Y', 'Y-m-d H:i:s'];
            
            foreach ($formats as $format) {
                try {
                    $parsedDate = Carbon::createFromFormat($format, $dateString);
                    if ($parsedDate !== false) {
                        return $parsedDate->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // If all formats fail, return current date
            return now()->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Date parsing failed for '{$dateString}': " . $e->getMessage());
            return now()->format('Y-m-d');
        }
    }

    /**
     * Detect CSV file type based on headers
     */
    private function detectCsvType($headers)
    {
        $headers = array_map('strtolower', $headers);
        
        if (in_array('identification_id', $headers) || in_array('passport_number', $headers)) {
            return 'identification_type';
        } elseif (in_array('register_number', $headers) && in_array('register_place', $headers)) {
            return 'register_type';
        } elseif (in_array('phone_number', $headers) && in_array('dob', $headers)) {
            return 'phone_type';
        }
        
        return 'unknown';
    }

    /**
     * Show a single ActivityUser record
     *
     * @OA\Get(
     *     path="/api/activity-users/{id}",
     *     summary="Get a single ActivityUser record",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ActivityUser found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $activityUser = ActivityUser::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $activityUser
        ]);
    }

    /**
     * Update an ActivityUser record
     *
     * @OA\Put(
     *     path="/api/activity-users/{id}",
     *     summary="Update an ActivityUser record",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="is_lead", type="boolean"),
     *             @OA\Property(property="invited", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        $request->validate([
            'is_lead' => 'boolean',
            'invited' => 'boolean'
        ]);

        $activityUser->update($request->only(['is_lead', 'invited']));

        return response()->json([
            'success' => true,
            'data' => $activityUser
        ]);
    }

    /**
     * Delete an ActivityUser record
     *
     * @OA\Delete(
     *     path="/api/activity-users/{id}",
     *     summary="Delete an ActivityUser record",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $activityUser = ActivityUser::findOrFail($id);
        $activityUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'ActivityUser deleted successfully'
        ]);
    }
}