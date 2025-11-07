<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResponsesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/responses",
     *     summary="Get all responses or a specific response by ID",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional response UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Responses retrieved successfully"),
     *     @OA\Response(response=404, description="Response not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $response = Response::where('response_id', $request->id)->first();
                if (!$response) {
                    return response()->json(['message' => 'Response not found'], 404);
                }
                return response()->json($response);
            }

            $responses = Response::all();
            return response()->json([
                'data' => $responses,
                'message' => 'Responses retrieved successfully'
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
     *     path="/api/responses",
     *     summary="Create a new response record",
     *     tags={"Responses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"survey_id","user_id"},
     *             @OA\Property(property="survey_id", type="string", format="uuid"),
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="submitted_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Response created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'survey_id' => 'required|uuid',
                'user_id' => 'required|uuid',
                'submitted_at' => 'nullable|date',
            ]);

            $response = Response::create([
                'response_id' => Str::uuid(),
                'survey_id' => $validated['survey_id'],
                'user_id' => $validated['user_id'],
                'submitted_at' => $validated['submitted_at'] ?? now(),
            ]);

            return response()->json([
                'data' => $response,
                'message' => 'Response created successfully'
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
     *     path="/api/responses/{id}",
     *     summary="Update an existing response",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Response UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="survey_id", type="string", format="uuid"),
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="submitted_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Response updated successfully"),
     *     @OA\Response(response=404, description="Response not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $response = Response::where('response_id', $id)->first();
            if (!$response) {
                return response()->json(['message' => 'Response not found'], 404);
            }

            $validated = $request->validate([
                'survey_id' => 'sometimes|uuid',
                'user_id' => 'sometimes|uuid',
                'submitted_at' => 'nullable|date',
            ]);

            $response->update($validated);

            return response()->json([
                'data' => $response,
                'message' => 'Response updated successfully'
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
     *     path="/api/responses/{id}",
     *     summary="Delete a response by UUID",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Response UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Response deleted successfully"),
     *     @OA\Response(response=404, description="Response not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $response = Response::where('response_id', $id)->first();
            if (!$response) {
                return response()->json(['message' => 'Response not found'], 404);
            }

            $response->delete();

            return response()->json(['message' => 'Response deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
