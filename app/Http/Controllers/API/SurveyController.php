<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API - Surveys",
     *      description="API documentation for Surveys Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/surveys",
     *     summary="Get all surveys or a specific survey by ID",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional survey UUID to fetch a specific survey",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Surveys retrieved successfully"),
     *     @OA\Response(response=404, description="Survey not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $survey = Survey::where('survey_id', $request->id)->first();
                if (!$survey) {
                    return response()->json(['message' => 'Survey not found'], 404);
                }
                return response()->json($survey);
            }

            $surveys = Survey::all();
            return response()->json([
                'data' => $surveys,
                'message' => 'Surveys retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unexpected error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/surveys",
     *     summary="Create a new survey",
     *     tags={"Surveys"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="End of Year Evaluation"),
     *             @OA\Property(property="link", type="string", example="https://example.com/survey-link"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Survey created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            'is_active' => 'boolean',
            'activity_id' => 'nullable|uuid|exists:activities,activity_id',
        ]);

        $survey = Survey::create([
    'survey_id' => Str::uuid(), // generate a UUID for the survey
    'description' => $validated['description'] ?? null,
    'link' => $validated['link'] ?? null,
    'is_active' => $validated['is_active'] ?? true,
    'activity_id' => $validated['activity_id'] ?? null, // use validated UUID
]);

        return response()->json([
            'data' => $survey,
            'message' => 'Survey created successfully'
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Unexpected error', 'error' => $e->getMessage()], 500);
    }
}

    /**
     * @OA\Put(
     *     path="/api/surveys/{id}",
     *     summary="Update an existing survey",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Survey UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Updated survey description"),
     *             @OA\Property(property="link", type="string", example="https://new-link.com"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Survey updated successfully"),
     *     @OA\Response(response=404, description="Survey not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $survey = Survey::where('survey_id', $id)->first();
            if (!$survey) {
                return response()->json(['message' => 'Survey not found'], 404);
            }

            $validated = $request->validate([
                'description' => 'nullable|string',
                'link' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $survey->update($validated);

            return response()->json([
                'data' => $survey,
                'message' => 'Survey updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unexpected error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/surveys/{id}",
     *     summary="Delete a survey by UUID",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Survey UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Survey deleted successfully"),
     *     @OA\Response(response=404, description="Survey not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $survey = Survey::where('survey_id', $id)->first();
            if (!$survey) {
                return response()->json(['message' => 'Survey not found'], 404);
            }

            $survey->delete();
            return response()->json(['message' => 'Survey deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unexpected error', 'error' => $e->getMessage()], 500);
        }
    }
}
