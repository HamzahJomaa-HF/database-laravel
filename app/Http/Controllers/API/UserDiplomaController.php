<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserDiploma;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     *         name="id",
     *         in="query",
     *         description="Optional UserDiploma ID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="UserDiplomas retrieved successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $userDiploma = UserDiploma::find($request->id);
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
     *     summary="Create a new user diploma",
     *     tags={"UserDiplomas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "diploma_id"},
     *             @OA\Property(property="user_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *             @OA\Property(property="diploma_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="UserDiploma created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|uuid',
                'diploma_id' => 'required|integer|exists:diplomas,id',
            ]);

            $userDiploma = UserDiploma::create([
                'user_id' => $validated['user_id'],
                'diploma_id' => $validated['diploma_id'],
            ]);

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
     * @OA\Put(
     *     path="/api/user-diplomas/{id}",
     *     summary="Update an existing user diploma",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UserDiploma ID to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="diploma_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="UserDiploma updated successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $userDiploma = UserDiploma::find($id);
            if (!$userDiploma) {
                return response()->json(['message' => 'UserDiploma not found'], 404);
            }

            $validated = $request->validate([
                'user_id' => 'sometimes|uuid',
                'diploma_id' => 'sometimes|integer|exists:diplomas,id',
            ]);

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
     *     path="/api/user-diplomas/{id}",
     *     summary="Delete a user diploma by ID",
     *     tags={"UserDiplomas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UserDiploma ID to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="UserDiploma deleted successfully"),
     *     @OA\Response(response=404, description="UserDiploma not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $userDiploma = UserDiploma::find($id);

            if (!$userDiploma) {
                return response()->json([
                    'message' => 'UserDiploma not found',
                    'id_sent' => $id,
                    'all_ids' => UserDiploma::pluck('id')
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
}
