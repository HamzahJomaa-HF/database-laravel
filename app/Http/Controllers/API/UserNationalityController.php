<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserNationality;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserNationalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-nationalities",
     *     summary="Get all user nationalities or a specific one by ID",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="users_nationality_id",
     *         in="query",
     *         description="Optional user nationality ID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User nationalities retrieved successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('users_nationality_id')) {
                $record = UserNationality::where('users_nationality_id', $request->users_nationality_id)->first();
                if (!$record) {
                    return response()->json(['message' => 'User nationality not found'], 404);
                }
                return response()->json($record);
            }

            $records = UserNationality::all();
            return response()->json([
                'data' => $records,
                'message' => 'User nationalities retrieved successfully'
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
     *     path="/api/user-nationalities",
     *     summary="Create a new user nationality relationship",
     *     tags={"UserNationalities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "nationality_id"},
     *             @OA\Property(property="user_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="nationality_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User nationality created successfully"),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=409, description="Relationship already exists")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => [
                    'required', 
                    'uuid', 
                    Rule::exists('users', 'user_id')
                ],
                'nationality_id' => [
                    'required', 
                    'uuid', 
                    Rule::exists('nationality', 'nationality_id')
                ],
            ]);

            // Check if relationship already exists
            $existing = UserNationality::where('user_id', $validated['user_id'])
                ->where('nationality_id', $validated['nationality_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'User nationality relationship already exists',
                    'data' => $existing
                ], 409);
            }

            $record = UserNationality::create($validated);

            return response()->json([
                'data' => $record,
                'message' => 'User nationality relationship created successfully'
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
     * @OA\Get(
     *     path="/api/users/{user_id}/nationalities",
     *     summary="Get all nationalities for a specific user",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID to get nationalities for",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User nationalities retrieved successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function getUserNationalities($user_id)
    {
        try {
            // Verify user exists
            if (!\App\Models\User::where('user_id', $user_id)->exists()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $nationalities = UserNationality::with('nationality')
                ->where('user_id', $user_id)
                ->get();

            return response()->json([
                'data' => $nationalities,
                'message' => 'User nationalities retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/nationalities/{nationality_id}/users",
     *     summary="Get all users for a specific nationality",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="nationality_id",
     *         in="path",
     *         description="Nationality ID to get users for",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Nationality users retrieved successfully"),
     *     @OA\Response(response=404, description="Nationality not found")
     * )
     */
    public function getNationalityUsers($nationality_id)
    {
        try {
            // Verify nationality exists
            if (!\App\Models\Nationality::where('nationality_id', $nationality_id)->exists()) {
                return response()->json(['message' => 'Nationality not found'], 404);
            }

            $users = UserNationality::with('user')
                ->where('nationality_id', $nationality_id)
                ->get();

            return response()->json([
                'data' => $users,
                'message' => 'Nationality users retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user-nationalities/{users_nationality_id}",
     *     summary="Update an existing user nationality relationship",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="users_nationality_id",
     *         in="path",
     *         description="User nationality relationship ID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="nationality_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User nationality updated successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function update(Request $request, $users_nationality_id)
    {
        try {
            $record = UserNationality::where('users_nationality_id', $users_nationality_id)->first();
            if (!$record) {
                return response()->json(['message' => 'User nationality relationship not found'], 404);
            }

            $validated = $request->validate([
                'user_id' => [
                    'sometimes', 
                    'uuid', 
                    Rule::exists('users', 'user_id')
                ],
                'nationality_id' => [
                    'sometimes', 
                    'uuid', 
                    Rule::exists('nationality', 'nationality_id')
                ],
            ]);

            // Check if new relationship would create a duplicate
            if (isset($validated['user_id']) || isset($validated['nationality_id'])) {
                $newUserId = $validated['user_id'] ?? $record->user_id;
                $newNationalityId = $validated['nationality_id'] ?? $record->nationality_id;

                $existing = UserNationality::where('user_id', $newUserId)
                    ->where('nationality_id', $newNationalityId)
                    ->where('users_nationality_id', '!=', $users_nationality_id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'message' => 'User nationality relationship already exists',
                        'data' => $existing
                    ], 409);
                }
            }

            $record->update($validated);

            return response()->json([
                'data' => $record,
                'message' => 'User nationality relationship updated successfully'
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
     *     path="/api/user-nationalities/{users_nationality_id}",
     *     summary="Delete a user nationality relationship by ID",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="users_nationality_id",
     *         in="path",
     *         description="User nationality relationship ID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User nationality deleted successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function destroy($users_nationality_id)
    {
        try {
            $record = UserNationality::where('users_nationality_id', $users_nationality_id)->first();
            if (!$record) {
                return response()->json(['message' => 'User nationality relationship not found'], 404);
            }

            $record->delete();

            return response()->json(['message' => 'User nationality relationship deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{user_id}/nationalities/{nationality_id}",
     *     summary="Delete a specific user nationality relationship",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="nationality_id",
     *         in="path",
     *         description="Nationality ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User nationality relationship deleted successfully"),
     *     @OA\Response(response=404, description="User nationality relationship not found")
     * )
     */
    public function destroySpecific($user_id, $nationality_id)
    {
        try {
            $record = UserNationality::where('user_id', $user_id)
                ->where('nationality_id', $nationality_id)
                ->first();

            if (!$record) {
                return response()->json(['message' => 'User nationality relationship not found'], 404);
            }

            $record->delete();

            return response()->json(['message' => 'User nationality relationship deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}