<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Programs Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/programs",
     *     summary="Get all programs or a specific program by ID",
     *     tags={"Programs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional program UUID to fetch a specific program",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Programs retrieved successfully"),
     *     @OA\Response(response=404, description="Program not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $program = Program::where('program_id', $request->id)->first();
                if (!$program) {
                    return response()->json(['message' => 'Program not found'], 404);
                }
                return response()->json($program);
            }

            $programs = Program::all();
            return response()->json([
                'data' => $programs,
                'message' => 'Programs retrieved successfully'
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
     *     path="/api/programs",
     *     summary="Create a new program",
     *     tags={"Programs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Youth Development"),
     *             @OA\Property(property="description", type="string", example="Program description here")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Program created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
   public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $program = Program::create([
                'program_id' => Str::uuid(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return response()->json([
                'data' => $program,
                'message' => 'Program created successfully'
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
     *     path="/api/programs/{id}",
     *     summary="Update an existing program",
     *     tags={"Programs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Program UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Program Name"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Program updated successfully"),
     *     @OA\Response(response=404, description="Program not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $program = Program::where('program_id', $id)->first();
            if (!$program) {
                return response()->json(['message' => 'Program not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);

            $program->update($validated);

            return response()->json([
                'data' => $program,
                'message' => 'Program updated successfully'
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
     *     path="/api/programs/{id}",
     *     summary="Delete a program by UUID",
     *     tags={"Programs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Program UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Program deleted successfully"),
     *     @OA\Response(response=404, description="Program not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $program = Program::where('program_id', $id)->first();
            if (!$program) {
                return response()->json(['message' => 'Program not found'], 404);
            }

            $program->delete();

            return response()->json(['message' => 'Program deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}