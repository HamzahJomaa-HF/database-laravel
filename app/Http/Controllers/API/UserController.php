<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Hariri Foundation API",
 *      description="API documentation for Users Management",
 *      @OA\Contact(email="support@haririfoundation.com")
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users or a specific user by UUID",
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
                return response()->json($user);
            }

            $users = User::all();
            return response()->json([
                'data' => $users,
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
     *     summary="Create a user (no update if exists)",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","dob","phone_number"},
     *             @OA\Property(property="identification_id", type="string", example="ID123456"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="middle_name", type="string", example="M."),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="mother_name", type="string", example="Jane"),
     *             @OA\Property(property="gender", type="string", example="Male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="register_number", type="string", example="R123456"),
     *             @OA\Property(property="phone_number", type="string", example="+96170000000"),
     *             @OA\Property(property="marital_status", type="string", example="Single"),
     *             @OA\Property(property="current_situation", type="string", example="Employed"),
     *             @OA\Property(property="passport_number", type="string", example="P987654")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=409, description="User already exists"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Unexpected error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'identification_id' => 'nullable|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'gender' => 'nullable|string|max:50',
                'dob' => 'required|date',
                'register_number' => 'nullable|string|max:255',
                'phone_number' => 'required|string|max:20',
                'marital_status' => 'nullable|string|max:50',
                'current_situation' => 'nullable|string|max:255',
                'passport_number' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user exists
            $existingUser = User::where('identification_id', $request->identification_id)
                ->orWhere(function ($q) use ($request) {
                    $q->where('dob', $request->dob)
                      ->where('phone_number', $request->phone_number);
                })
                ->first();

            if ($existingUser) {
                return response()->json([
                    'message' => 'User already exists'
                ], 409); // Conflict
            }

            // Create new user
            $newUser = User::create([
                'user_id' => Str::uuid(),
                'identification_id' => $request->identification_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'mother_name' => $request->mother_name,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'register_number' => $request->register_number,
                'phone_number' => $request->phone_number,
                'marital_status' => $request->marital_status,
                'current_situation' => $request->current_situation,
                'passport_number' => $request->passport_number,
            ]);

            return response()->json([
                'data' => $newUser,
                'message' => 'User created successfully'
            ], 201);

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
     *             @OA\Property(property="identification_id", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="middle_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="mother_name", type="string"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="dob", type="string", format="date"),
     *             @OA\Property(property="register_number", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="marital_status", type="string"),
     *             @OA\Property(property="current_situation", type="string"),
     *             @OA\Property(property="passport_number", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Unexpected error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::where('user_id', $id)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validated = $request->validate([
                'identification_id' => 'sometimes|string|max:255',
                'first_name' => 'sometimes|string|max:255',
                'middle_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'mother_name' => 'sometimes|string|max:255',
                'gender' => 'sometimes|string|max:50',
                'dob' => 'sometimes|date',
                'register_number' => 'sometimes|string|max:255',
                'phone_number' => 'sometimes|string|max:20',
                'marital_status' => 'sometimes|string|max:50',
                'current_situation' => 'sometimes|string|max:255',
                'passport_number' => 'sometimes|string|max:50',
            ]);

            $user->update($validated);

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
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Unexpected error")
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::where('user_id', $id)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
