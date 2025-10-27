<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CopController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for COPs Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/cops",
     *     summary="Get all COPs or a specific COP by ID",
     *     tags={"COPs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional COP UUID to fetch a specific COP",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="COPs retrieved successfully"),
     *     @OA\Response(response=404, description="COP not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $cop = Cop::where('cop_id', $request->id)->first();
                if (!$cop) {
                    return response()->json(['message' => 'COP not found'], 404);
                }
                return response()->json($cop);
            }

            $cops = Cop::all();
            return response()->json([
                'data' => $cops,
                'message' => 'COPs retrieved successfully'
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
     *     path="/api/cops",
     *     summary="Create a new COP",
     *     tags={"COPs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"program_id","cop_name"},
     *             @OA\Property(property="program_id", type="string", format="uuid", example="uuid-of-program"),
     *             @OA\Property(property="cop_name", type="string", example="Youth COP"),
     *             @OA\Property(property="description", type="string", example="COP description here")
     *         )
     *     ),
     *     @OA\Response(response=201, description="COP created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'program_id' => 'required|uuid|exists:programs,program_id',
                'cop_name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $cop = Cop::create([
                'cop_id' => Str::uuid(),
                'program_id' => $validated['program_id'],
                'cop_name' => $validated['cop_name'],
                'description' => $validated['description'] ?? null,
            ]);

            return response()->json([
                'data' => $cop,
                'message' => 'COP created successfully'
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
     *     path="/api/cops/{id}",
     *     summary="Update an existing COP",
     *     tags={"COPs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="COP UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="program_id", type="string", format="uuid"),
     *             @OA\Property(property="cop_name", type="string", example="Updated COP Name"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(response=200, description="COP updated successfully"),
     *     @OA\Response(response=404, description="COP not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $cop = Cop::where('cop_id', $id)->first();
            if (!$cop) {
                return response()->json(['message' => 'COP not found'], 404);
            }

            $validated = $request->validate([
                'program_id' => 'sometimes|uuid|exists:programs,program_id',
                'cop_name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);

            $cop->update($validated);

            return response()->json([
                'data' => $cop,
                'message' => 'COP updated successfully'
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
     *     path="/api/cops/{id}",
     *     summary="Delete a COP by UUID",
     *     tags={"COPs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="COP UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="COP deleted successfully"),
     *     @OA\Response(response=404, description="COP not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $cop = Cop::where('cop_id', $id)->first();
            if (!$cop) {
                return response()->json(['message' => 'COP not found'], 404);
            }

            $cop->delete();

            return response()->json(['message' => 'COP deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
