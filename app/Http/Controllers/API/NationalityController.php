<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use Illuminate\Http\Request;

class NationalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/nationalities",
     *     summary="Get all nationalities or a specific nationality by ID",
     *     tags={"Nationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional nationality UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Nationalities retrieved successfully"),
     *     @OA\Response(response=404, description="Nationality not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $nationality = Nationality::find($request->id);
                if (!$nationality) {
                    return response()->json(['message' => 'Nationality not found'], 404);
                }
                return response()->json($nationality);
            }

            $nationalities = Nationality::all();
            return response()->json([
                'data' => $nationalities,
                'message' => 'Nationalities retrieved successfully'
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
     *     path="/api/nationalities",
     *     summary="Create a new nationality",
     *     tags={"Nationalities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Lebanese")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Nationality created successfully"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
            ]);

            $nationality = Nationality::create($validated);

            return response()->json([
                'data' => $nationality,
                'message' => 'Nationality created successfully'
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
     *     path="/api/nationalities/{id}",
     *     summary="Update an existing nationality",
     *     tags={"Nationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Nationality UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Nationality Name")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Nationality updated successfully"),
     *     @OA\Response(response=404, description="Nationality not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $nationality = Nationality::find($id);
            if (!$nationality) {
                return response()->json(['message' => 'Nationality not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:100',
            ]);

            $nationality->update($validated);

            return response()->json([
                'data' => $nationality,
                'message' => 'Nationality updated successfully'
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
     *     path="/api/nationalities/{id}",
     *     summary="Delete a nationality by UUID",
     *     tags={"Nationalities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Nationality UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Nationality deleted successfully"),
     *     @OA\Response(response=404, description="Nationality not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $nationality = Nationality::find($id);
            if (!$nationality) {
                return response()->json(['message' => 'Nationality not found'], 404);
            }

            $nationality->delete();

            return response()->json(['message' => 'Nationality deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
