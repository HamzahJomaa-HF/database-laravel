<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Diploma;
use Illuminate\Http\Request;

class DiplomaController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Diploma API",
     *      description="API documentation for Diploma Management",
     *      @OA\Contact(email="support@example.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/diplomas",
     *     summary="Get all diplomas or a specific diploma by ID",
     *     tags={"Diplomas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional diploma ID to fetch a specific diploma",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Diplomas retrieved successfully"),
     *     @OA\Response(response=404, description="Diploma not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $diploma = Diploma::find($request->id);
                if (!$diploma) {
                    return response()->json(['message' => 'Diploma not found'], 404);
                }
                return response()->json($diploma);
            }

            $diplomas = Diploma::all();
            return response()->json([
                'data' => $diplomas,
                'message' => 'Diplomas retrieved successfully'
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
     *     path="/api/diplomas",
     *     summary="Create a new diploma",
     *     tags={"Diplomas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"diploma_name"},
     *             @OA\Property(property="diploma_name", type="string", example="Bachelor of Science"),
     *             @OA\Property(property="institution", type="string", example="Hariri University"),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="external_id", type="string", example="EXT12345")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Diploma created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'diploma_name' => 'required|string|max:255',
                'institution' => 'nullable|string|max:255',
                'year' => 'nullable|integer',
                'external_id' => 'nullable|string|max:255',
            ]);

            $diploma = Diploma::create($validated);

            return response()->json([
                'data' => $diploma,
                'message' => 'Diploma created successfully'
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
     *     path="/api/diplomas/{id}",
     *     summary="Update an existing diploma",
     *     tags={"Diplomas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Diploma ID to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="diploma_name", type="string", example="Updated Diploma Name"),
     *             @OA\Property(property="institution", type="string", example="Updated University"),
     *             @OA\Property(property="year", type="integer", example=2021),
     *             @OA\Property(property="external_id", type="string", example="EXT67890")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Diploma updated successfully"),
     *     @OA\Response(response=404, description="Diploma not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $diploma = Diploma::find($id);
            if (!$diploma) {
                return response()->json(['message' => 'Diploma not found'], 404);
            }

            $validated = $request->validate([
                'diploma_name' => 'sometimes|required|string|max:255',
                'institution' => 'nullable|string|max:255',
                'year' => 'nullable|integer',
                'external_id' => 'nullable|string|max:255',
            ]);

            $diploma->update($validated);

            return response()->json([
                'data' => $diploma,
                'message' => 'Diploma updated successfully'
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
     *     path="/api/diplomas/{id}",
     *     summary="Delete a diploma by ID",
     *     tags={"Diplomas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Diploma ID to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Diploma deleted successfully"),
     *     @OA\Response(response=404, description="Diploma not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $diploma = Diploma::find($id);
            if (!$diploma) {
                return response()->json(['message' => 'Diploma not found'], 404);
            }

            $diploma->delete();

            return response()->json(['message' => 'Diploma deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
