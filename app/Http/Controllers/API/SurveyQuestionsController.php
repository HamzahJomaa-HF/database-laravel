<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyQuestionsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/survey-questions",
     *     summary="Get all survey questions or a specific survey question by ID",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional survey_question UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Survey questions retrieved successfully"),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $surveyQuestion = SurveyQuestion::where('survey_question_id', $request->id)->first();
                if (!$surveyQuestion) {
                    return response()->json(['message' => 'Survey question not found'], 404);
                }
                return response()->json($surveyQuestion);
            }

            $surveyQuestions = SurveyQuestion::all();
            return response()->json([
                'data' => $surveyQuestions,
                'message' => 'Survey questions retrieved successfully'
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
     *     path="/api/survey-questions",
     *     summary="Create a new survey question record",
     *     tags={"SurveyQuestions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"survey_id","question_id","question_order"},
     *             @OA\Property(property="survey_id", type="string", format="uuid"),
     *             @OA\Property(property="question_id", type="string", format="uuid"),
     *             @OA\Property(property="question_order", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Survey question created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'survey_id' => 'required|uuid',
                'question_id' => 'required|uuid',
                'question_order' => 'required|integer|min:1',
            ]);

            $surveyQuestion = SurveyQuestion::create([
                'survey_question_id' => Str::uuid(),
                'survey_id' => $validated['survey_id'],
                'question_id' => $validated['question_id'],
                'question_order' => $validated['question_order'],
            ]);

            return response()->json([
                'data' => $surveyQuestion,
                'message' => 'Survey question created successfully'
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
     *     path="/api/survey-questions/{id}",
     *     summary="Update an existing survey question record",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Survey question UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="survey_id", type="string", format="uuid"),
     *             @OA\Property(property="question_id", type="string", format="uuid"),
     *             @OA\Property(property="question_order", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Survey question updated successfully"),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $surveyQuestion = SurveyQuestion::where('survey_question_id', $id)->first();
            if (!$surveyQuestion) {
                return response()->json(['message' => 'Survey question not found'], 404);
            }

            $validated = $request->validate([
                'survey_id' => 'sometimes|uuid',
                'question_id' => 'sometimes|uuid',
                'question_order' => 'sometimes|integer|min:1',
            ]);

            $surveyQuestion->update($validated);

            return response()->json([
                'data' => $surveyQuestion,
                'message' => 'Survey question updated successfully'
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
     *     path="/api/survey-questions/{id}",
     *     summary="Delete a survey question record by UUID",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Survey question UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Survey question deleted successfully"),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $surveyQuestion = SurveyQuestion::where('survey_question_id', $id)->first();
            if (!$surveyQuestion) {
                return response()->json(['message' => 'Survey question not found'], 404);
            }

            $surveyQuestion->delete();

            return response()->json(['message' => 'Survey question deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
