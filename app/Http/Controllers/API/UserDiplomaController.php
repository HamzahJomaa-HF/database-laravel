<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserDiploma;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserDiplomaController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for UserDiploma Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/user-diplomas",
     *     summary="Get all user diplomas or a specific one by ID",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="users_diploma_id",
     *         in="query",
     *         description="Optional UserDiploma ID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="UserDiplomas retrieved successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('users_diploma_id')) {
                $userDiploma = UserDiploma::where('users_diploma_id', $request->users_diploma_id)->first();
                if (!$userDiploma) {
                    return response()->json(['message' => 'UserDiploma not found'], 404);
                }
                return response()->json($userDiploma);
            }

            $userDiplomas = UserDiploma::all();
            return response()->json([
                'data' => $userDiplomas,
                'message' => 'UserDiplomas retrieved successfully'
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
     *     path="/api/user-diplomas",
     *     summary="Create a new user diploma relationship",
     *     tags={"UserDiplomas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "diploma_id"},
     *             @OA\Property(property="user_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *             @OA\Property(property="diploma_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d480")
     *         )
     *     ),
     *     @OA\Response(response=201, description="UserDiploma created successfully"),
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
                'diploma_id' => [
                    'required', 
                    'uuid', 
                    Rule::exists('diploma', 'diploma_id')
                ],
            ]);

            // Check if relationship already exists
            $existing = UserDiploma::where('user_id', $validated['user_id'])
                ->where('diploma_id', $validated['diploma_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'User diploma relationship already exists',
                    'data' => $existing
                ], 409);
            }

            $userDiploma = UserDiploma::create($validated);

            return response()->json([
                'data' => $userDiploma,
                'message' => 'UserDiploma created successfully'
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
     *     path="/api/users/{user_id}/diplomas",
     *     summary="Get all diplomas for a specific user",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID to get diplomas for",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User diplomas retrieved successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function getUserDiplomas($user_id)
    {
        try {
            // Verify user exists
            if (!\App\Models\User::where('user_id', $user_id)->exists()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $diplomas = UserDiploma::with('diploma')
                ->where('user_id', $user_id)
                ->get();

            return response()->json([
                'data' => $diplomas,
                'message' => 'User diplomas retrieved successfully'
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
     *     path="/api/diplomas/{diploma_id}/users",
     *     summary="Get all users for a specific diploma",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="diploma_id",
     *         in="path",
     *         description="Diploma ID to get users for",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Diploma users retrieved successfully"),
     *     @OA\Response(response=404, description="Diploma not found")
     * )
     */
    public function getDiplomaUsers($diploma_id)
    {
        try {
            // Verify diploma exists
            if (!\App\Models\Diploma::where('diploma_id', $diploma_id)->exists()) {
                return response()->json(['message' => 'Diploma not found'], 404);
            }

            $users = UserDiploma::with('user')
                ->where('diploma_id', $diploma_id)
                ->get();

            return response()->json([
                'data' => $users,
                'message' => 'Diploma users retrieved successfully'
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
     *     path="/api/user-diplomas/{users_diploma_id}",
     *     summary="Update an existing user diploma relationship",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="users_diploma_id",
     *         in="path",
     *         description="UserDiploma relationship ID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *             @OA\Property(property="diploma_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d480")
     *         )
     *     ),
     *     @OA\Response(response=200, description="UserDiploma updated successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function update(Request $request, $users_diploma_id)
    {
        try {
            $userDiploma = UserDiploma::where('users_diploma_id', $users_diploma_id)->first();
            if (!$userDiploma) {
                return response()->json(['message' => 'UserDiploma not found'], 404);
            }

            $validated = $request->validate([
                'user_id' => [
                    'sometimes', 
                    'uuid', 
                    Rule::exists('users', 'user_id')
                ],
                'diploma_id' => [
                    'sometimes', 
                    'uuid', 
                    Rule::exists('diploma', 'diploma_id')
                ],
            ]);

            // Check if new relationship would create a duplicate
            if (isset($validated['user_id']) || isset($validated['diploma_id'])) {
                $newUserId = $validated['user_id'] ?? $userDiploma->user_id;
                $newDiplomaId = $validated['diploma_id'] ?? $userDiploma->diploma_id;

                $existing = UserDiploma::where('user_id', $newUserId)
                    ->where('diploma_id', $newDiplomaId)
                    ->where('users_diploma_id', '!=', $users_diploma_id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'message' => 'User diploma relationship already exists',
                        'data' => $existing
                    ], 409);
                }
            }

            $userDiploma->update($validated);

            return response()->json([
                'data' => $userDiploma,
                'message' => 'UserDiploma updated successfully'
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
     *     path="/api/user-diplomas/{users_diploma_id}",
     *     summary="Delete a user diploma relationship by ID",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="users_diploma_id",
     *         in="path",
     *         description="UserDiploma relationship ID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="UserDiploma deleted successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function destroy($users_diploma_id)
    {
        try {
            $userDiploma = UserDiploma::where('users_diploma_id', $users_diploma_id)->first();

            if (!$userDiploma) {
                return response()->json([
                    'message' => 'UserDiploma not found',
                    'users_diploma_id_sent' => $users_diploma_id
                ], 404);
            }

            $userDiploma->delete();

            return response()->json(['message' => 'UserDiploma deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{user_id}/diplomas/{diploma_id}",
     *     summary="Delete a specific user diploma relationship",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="diploma_id",
     *         in="path",
     *         description="Diploma ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="User diploma relationship deleted successfully"),
     *     @OA\Response(response=404, description="User diploma relationship not found")
     * )
     */
    public function destroySpecific($user_id, $diploma_id)
    {
        try {
            $userDiploma = UserDiploma::where('user_id', $user_id)
                ->where('diploma_id', $diploma_id)
                ->first();

            if (!$userDiploma) {
                return response()->json(['message' => 'User diploma relationship not found'], 404);
            }

            $userDiploma->delete();

            return response()->json(['message' => 'User diploma relationship deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}