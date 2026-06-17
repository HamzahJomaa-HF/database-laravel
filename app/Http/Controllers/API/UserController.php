<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Diploma;
use App\Models\Nationality;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Http\Requests\BulkDeleteUserRequest;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Hariri Foundation API",
 *      description="API documentation for Users Management",
 *      @OA\Contact(email="support@haririfoundation.com")
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users or a specific user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional user UUID to fetch a specific user",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Users retrieved successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
   public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $user = User::where('user_id', $request->id)->first();
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 404);
                }
                // Remove the 'type' field from the response
                $userData = $user->toArray();
                unset($userData['type']);
                return response()->json($userData);
            }

            $users = User::all();
            // Remove the 'type' field from each user in the collection
            $usersData = $users->map(function ($user) {
                $userData = $user->toArray();
                unset($userData['type']);
                return $userData;
            });
            
            return response()->json([
                'data' => $usersData,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "gender", "position_1", "organization_1", "organization_type_1", "status_1", "address", "phone_number", "scope", "is_high_profile"},
     *             @OA\Property(property="prefix", type="string", maxLength=50, example="Dr.", description="Title prefix (Mr., Dr., Prof., etc.)"),
     *             @OA\Property(property="is_high_profile", type="boolean", example=true, description="Whether the user is a high-profile individual"),
     *             @OA\Property(property="scope", type="string", enum={"International", "Regional", "National", "Local"}, example="National", description="Scope of influence/operation"),
     *             @OA\Property(property="default_cop_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000", description="Default Community of Practice ID"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="First name"),
     *             @OA\Property(property="middle_name", type="string", maxLength=255, nullable=true, example="William", description="Middle name"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe", description="Last name"),
     *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male", description="Gender"),
     *             @OA\Property(property="dob", type="string", format="date", nullable=true, example="1990-01-15", description="Date of birth"),
     *             @OA\Property(property="position_1", type="string", maxLength=255, example="Senior Director", description="Primary position"),
     *             @OA\Property(property="organization_1", type="string", maxLength=255, example="Hariri Foundation", description="Primary organization"),
     *             @OA\Property(property="organization_type_1", type="string", enum={"Public Sector", "Private Sector", "Academia", "UN", "INGOs", "Civil Society", "NGOs", "Activist"}, example="Private Sector", description="Primary organization type"),
     *             @OA\Property(property="status_1", type="string", maxLength=255, example="Active", description="Primary position status"),
     *             @OA\Property(property="sector", type="string", maxLength=255, nullable=true, example="Healthcare", description="Sector of work"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, Beirut, Lebanon", description="Physical address"),
     *             @OA\Property(property="phone_number", type="string", maxLength=20, example="+961 70 123 456", description="Primary phone number"),
     *             @OA\Property(property="office_phone", type="string", maxLength=20, nullable=true, example="+961 1 234 567", description="Office phone number"),
     *             @OA\Property(property="extension_number", type="string", maxLength=20, nullable=true, example="1234", description="Phone extension number"),
     *             @OA\Property(property="home_phone", type="string", maxLength=20, nullable=true, example="+961 5 678 901", description="Home phone number"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, nullable=true, example="john.doe@example.com", description="Email address"),
     *             @OA\Property(property="position_2", type="string", maxLength=255, nullable=true, example="Board Member", description="Secondary position"),
     *             @OA\Property(property="organization_2", type="string", maxLength=255, nullable=true, example="NGO Name", description="Secondary organization"),
     *             @OA\Property(property="organization_type_2", type="string", enum={"Public Sector", "Private Sector", "Academia", "UN", "INGOs", "Civil Society", "NGOs", "Activist"}, nullable=true, description="Secondary organization type"),
     *             @OA\Property(property="status_2", type="string", maxLength=255, nullable=true, example="Volunteer", description="Secondary position status"),
     *             @OA\Property(property="mother_name", type="string", maxLength=255, nullable=true, example="Jane Doe", description="Mother's name"),
     *             @OA\Property(property="original_name", type="string", maxLength=255, nullable=true, example="أحمد علي", description="Original name (e.g. in Arabic or native script)"),
     *             @OA\Property(property="marital_status", type="string", maxLength=50, nullable=true, enum={"Single", "Married", "Divorced", "Widowed"}, example="Married", description="Marital status"),
     *             @OA\Property(property="employment_status", type="string", maxLength=50, nullable=true, enum={"Full-time", "Part-time", "Contract", "Freelance", "Unemployed", "Retired"}, example="Full-time", description="Employment status"),
     *             @OA\Property(property="type", type="string", maxLength=50, enum={"Stakeholder", "Employee", "Admin", "Customer", "Partner", "Beneficiary"}, example="Stakeholder", description="User type"),
     *             @OA\Property(property="identification_id", type="string", maxLength=50, nullable=true, example="ID12345678", description="National identification number"),
     *             @OA\Property(property="passport_number", type="string", maxLength=50, nullable=true, example="P12345678", description="Passport number"),
     *             @OA\Property(property="register_number", type="string", maxLength=50, nullable=true, example="REG12345", description="Registration number"),
     *             @OA\Property(property="register_place", type="string", maxLength=255, nullable=true, example="Beirut", description="Place of registration"),
     *             @OA\Property(
     *                 property="diplomas",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 description="Array of diploma IDs"
     *             ),
     *             @OA\Property(
     *                 property="nationalities",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 description="Array of nationality IDs"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                // Required fields from new structure
                'prefix' => 'nullable|string|max:50',
                'is_high_profile' => 'required|boolean',
                'scope' => ['required', Rule::in(['International', 'Regional', 'National', 'Local'])],
                'default_cop_id' => 'nullable|exists:cops,cop_id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
                'position_1' => 'required|string|max:255',
                'organization_1' => 'required|string|max:255',
                'organization_type_1' => [
                    'required',
                    Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
                ],
                'status_1' => 'required|string|max:255',
                'address' => 'required|string',
                'phone_number' => 'required|string|max:20',
                
                // Optional fields from new structure
                'sector' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'office_phone' => 'nullable|string|max:20',
                'extension_number' => 'nullable|string|max:20',
                'home_phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255|unique:users,email',
                
                // Optional secondary position fields
                'position_2' => 'nullable|string|max:255',
                'organization_2' => 'nullable|string|max:255',
                'organization_type_2' => [
                    'nullable',
                    Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
                ],
                'status_2' => 'nullable|string|max:255',
                
                // Keep existing fields for backward compatibility
                'mother_name' => 'nullable|string|max:255',
                'original_name' => 'nullable|string|max:255',
                'marital_status' => 'nullable|string|max:50',
                'employment_status' => 'nullable|string|max:50',
                'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner,Beneficiary',
                'identification_id' => 'nullable|string|max:50|unique:users,identification_id',
                'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
                'register_number' => 'nullable|string|max:50',
                'register_place' => 'nullable|string|max:255',

                // Diploma and Nationality fields
                'diplomas' => 'nullable|array',
                'diplomas.*' => 'exists:diploma,diploma_id',
                'nationalities' => 'nullable|array',
                'nationalities.*' => 'exists:nationality,nationality_id',
            ];

            $validated = $request->validate($rules);

            // Prepare user data
            $userData = $request->only([
                // New required fields
                'prefix', 'is_high_profile', 'scope', 'default_cop_id',
                'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
                'organization_type_1', 'status_1', 'address', 'phone_number',

                // New optional fields
                'sector', 'middle_name', 'dob', 'office_phone', 'extension_number',
                'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2', 'status_2',

                // Existing fields
                'mother_name', 'original_name', 'marital_status',
                'employment_status', 'type', 'identification_id', 'passport_number',
                'register_number', 'register_place'
            ]);

            // Set default type if not provided
            if (empty($userData['type'])) {
                $userData['type'] = 'Stakeholder';
            }
            
            // Create user
            $user = User::create($userData);
            
            // Sync diplomas with existing diploma records
            if ($request->has('diplomas')) {
                $user->diplomas()->sync($request->diplomas);
            }
            
            // Sync nationalities with existing nationality records
            if ($request->has('nationalities')) {
                $user->nationalities()->sync($request->nationalities);
            }

            return response()->json([
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="prefix", type="string", maxLength=50, example="Dr.", description="Title prefix"),
     *             @OA\Property(property="is_high_profile", type="boolean", example=true, description="Whether the user is a high-profile individual"),
     *             @OA\Property(property="scope", type="string", enum={"International", "Regional", "National", "Local"}, example="National", description="Scope of influence/operation"),
     *             @OA\Property(property="default_cop_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000", description="Default Community of Practice ID"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="First name"),
     *             @OA\Property(property="middle_name", type="string", maxLength=255, nullable=true, example="William", description="Middle name"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe", description="Last name"),
     *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male", description="Gender"),
     *             @OA\Property(property="dob", type="string", format="date", nullable=true, example="1990-01-15", description="Date of birth"),
     *             @OA\Property(property="position_1", type="string", maxLength=255, example="Senior Director", description="Primary position"),
     *             @OA\Property(property="organization_1", type="string", maxLength=255, example="Hariri Foundation", description="Primary organization"),
     *             @OA\Property(property="organization_type_1", type="string", enum={"Public Sector", "Private Sector", "Academia", "UN", "INGOs", "Civil Society", "NGOs", "Activist"}, example="Private Sector", description="Primary organization type"),
     *             @OA\Property(property="status_1", type="string", maxLength=255, example="Active", description="Primary position status"),
     *             @OA\Property(property="sector", type="string", maxLength=255, nullable=true, example="Healthcare", description="Sector of work"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, Beirut, Lebanon", description="Physical address"),
     *             @OA\Property(property="phone_number", type="string", maxLength=20, example="+961 70 123 456", description="Primary phone number"),
     *             @OA\Property(property="office_phone", type="string", maxLength=20, nullable=true, example="+961 1 234 567", description="Office phone number"),
     *             @OA\Property(property="extension_number", type="string", maxLength=20, nullable=true, example="1234", description="Phone extension number"),
     *             @OA\Property(property="home_phone", type="string", maxLength=20, nullable=true, example="+961 5 678 901", description="Home phone number"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, nullable=true, example="john.doe@example.com", description="Email address"),
     *             @OA\Property(property="position_2", type="string", maxLength=255, nullable=true, example="Board Member", description="Secondary position"),
     *             @OA\Property(property="organization_2", type="string", maxLength=255, nullable=true, example="NGO Name", description="Secondary organization"),
     *             @OA\Property(property="organization_type_2", type="string", enum={"Public Sector", "Private Sector", "Academia", "UN", "INGOs", "Civil Society", "NGOs", "Activist"}, nullable=true, description="Secondary organization type"),
     *             @OA\Property(property="status_2", type="string", maxLength=255, nullable=true, example="Volunteer", description="Secondary position status"),
     *             @OA\Property(property="mother_name", type="string", maxLength=255, nullable=true, example="Jane Doe", description="Mother's name"),
     *             @OA\Property(property="original_name", type="string", maxLength=255, nullable=true, example="أحمد علي", description="Original name (e.g. in Arabic or native script)"),
     *             @OA\Property(property="marital_status", type="string", maxLength=50, nullable=true, enum={"Single", "Married", "Divorced", "Widowed"}, example="Married", description="Marital status"),
     *             @OA\Property(property="employment_status", type="string", maxLength=50, nullable=true, enum={"Full-time", "Part-time", "Contract", "Freelance", "Unemployed", "Retired"}, example="Full-time", description="Employment status"),
     *             @OA\Property(property="type", type="string", maxLength=50, enum={"Stakeholder", "Employee", "Admin", "Customer", "Partner", "Beneficiary"}, example="Stakeholder", description="User type"),
     *             @OA\Property(property="identification_id", type="string", maxLength=50, nullable=true, example="ID12345678", description="National identification number"),
     *             @OA\Property(property="passport_number", type="string", maxLength=50, nullable=true, example="P12345678", description="Passport number"),
     *             @OA\Property(property="register_number", type="string", maxLength=50, nullable=true, example="REG12345", description="Registration number"),
     *             @OA\Property(property="register_place", type="string", maxLength=255, nullable=true, example="Beirut", description="Place of registration"),
     *             @OA\Property(
     *                 property="diplomas",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 description="Array of diploma IDs"
     *             ),
     *             @OA\Property(
     *                 property="nationalities",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 description="Array of nationality IDs"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, $user_id)
    {
        try {
            $user = User::where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $rules = [
                // Required fields from new structure
                'prefix' => 'nullable|string|max:50',
                'is_high_profile' => 'sometimes|required|boolean',
                'scope' => ['sometimes', 'required', Rule::in(['International', 'Regional', 'National', 'Local'])],
                'default_cop_id' => 'nullable|exists:cops,cop_id',
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'gender' => ['sometimes', 'required', Rule::in(['Male', 'Female', 'Other'])],
                'position_1' => 'sometimes|required|string|max:255',
                'organization_1' => 'sometimes|required|string|max:255',
                'organization_type_1' => [
                    'sometimes',
                    'required',
                    Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
                ],
                'status_1' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string',
                'phone_number' => 'sometimes|required|string|max:20',
                
                // Optional fields from new structure
                'sector' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'office_phone' => 'nullable|string|max:20',
                'extension_number' => 'nullable|string|max:20',
                'home_phone' => 'nullable|string|max:20',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
                ],
                
                // Optional secondary position fields
                'position_2' => 'nullable|string|max:255',
                'organization_2' => 'nullable|string|max:255',
                'organization_type_2' => [
                    'nullable',
                    Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
                ],
                'status_2' => 'nullable|string|max:255',
                
                // Keep existing fields for backward compatibility
                'mother_name' => 'nullable|string|max:255',
                'original_name' => 'nullable|string|max:255',
                'marital_status' => 'nullable|string|max:50',
                'employment_status' => 'nullable|string|max:50',
                'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner,Beneficiary',
                'identification_id' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('users', 'identification_id')->ignore($user->user_id, 'user_id'),
                ],
                'passport_number' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('users', 'passport_number')->ignore($user->user_id, 'user_id'),
                ],
                'register_number' => 'nullable|string|max:50',
                'register_place' => 'nullable|string|max:255',

                // Diploma and Nationality fields
                'diplomas' => 'nullable|array',
                'diplomas.*' => 'exists:diploma,diploma_id',
                'nationalities' => 'nullable|array',
                'nationalities.*' => 'exists:nationality,nationality_id',
            ];

            $validated = $request->validate($rules);

            // Prepare user data
            $userData = $request->only([
                // New required fields
                'prefix', 'is_high_profile', 'scope', 'default_cop_id',
                'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
                'organization_type_1', 'status_1', 'address', 'phone_number',

                // New optional fields
                'sector', 'middle_name', 'dob', 'office_phone', 'extension_number',
                'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2', 'status_2',

                // Existing fields
                'mother_name', 'original_name', 'marital_status',
                'employment_status', 'type', 'identification_id', 'passport_number',
                'register_number', 'register_place'
            ]);

            $user->update($userData);
            
            // Sync diplomas
            if ($request->has('diplomas')) {
                $user->diplomas()->sync($request->diplomas ?? []);
            }
            
            // Sync nationalities
            if ($request->has('nationalities')) {
                $user->nationalities()->sync($request->nationalities ?? []);
            }

            return response()->json([
                'data' => $user,
                'message' => 'User updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user by UUID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($user_id)
    {
        try {
            $user = User::where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->diplomas()->detach();
            $user->nationalities()->detach();
            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/bulk-delete",
     *     summary="Bulk delete users",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_ids"},
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *                 @OA\Items(type="string", format="uuid"),
     *                 description="Array of user UUIDs to delete",
     *                 example={"123e4567-e89b-12d3-a456-426614174000", "987e6543-b21d-43c1-a654-123456789abc"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Users deleted successfully"),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function bulkDestroy(BulkDeleteUserRequest $request)
    {
        try {
            $userIds = $request->getUserIdsAsArray();
            
            DB::transaction(function () use ($userIds) {
                foreach ($userIds as $userId) {
                    $user = User::where('user_id', $userId)->first();
                    if ($user) {
                        $user->diplomas()->detach();
                        $user->nationalities()->detach();
                        $user->delete();
                    }
                }
            });
            
            $count = count($userIds);
            
            return response()->json([
                'message' => "Successfully deleted {$count} user(s).",
                'deleted_count' => $count
            ]);
                
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete users: ' . $e->getMessage()
            ], 500);
        }
    }
}