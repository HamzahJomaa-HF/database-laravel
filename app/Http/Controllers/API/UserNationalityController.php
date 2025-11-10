<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserNationality;
use Illuminate\Http\Request;

class UserNationalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-nationalities",
     *     summary="Get all user nationalities or a specific one by ID",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional user nationality ID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User nationalities retrieved successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $record = UserNationality::find($request->id);
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
     *     summary="Create a new user nationality",
     *     tags={"UserNationalities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "nationality_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="nationality_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=201, description="User nationality created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'nationality_id' => 'required|integer|exists:nationality,id',
            ]);

            $record = UserNationality::create($validated);

            return response()->json([
                'data' => $record,
                'message' => 'User nationality created successfully'
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
     *     path="/api/user-nationalities/{id}",
     *     summary="Update an existing user nationality",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User nationality ID to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="nationality_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User nationality updated successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $record = UserNationality::find($id);
            if (!$record) {
                return response()->json(['message' => 'User nationality not found'], 404);
            }

            $validated = $request->validate([
                'user_id' => 'sometimes|integer|exists:users,id',
                'nationality_id' => 'sometimes|integer|exists:nationality,id',
            ]);

            $record->update($validated);

            return response()->json([
                'data' => $record,
                'message' => 'User nationality updated successfully'
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
     *     path="/api/user-nationalities/{id}",
     *     summary="Delete a user nationality by ID",
     *     tags={"UserNationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User nationality ID to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User nationality deleted successfully"),
     *     @OA\Response(response=404, description="User nationality not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $record = UserNationality::find($id);
            if (!$record) {
                return response()->json(['message' => 'User nationality not found'], 404);
            }

            $record->delete();

            return response()->json(['message' => 'User nationality deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
