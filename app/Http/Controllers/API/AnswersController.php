<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnswersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/answers",
     *     summary="Get all answers or a specific answer by ID",
     *     tags={"Answers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional answer UUID to fetch a specific answer",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Answers retrieved successfully"),
     *     @OA\Response(response=404, description="Answer not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $answer = Answer::where('answer_id', $request->id)->first();
                if (!$answer) {
                    return response()->json(['message' => 'Answer not found'], 404);
                }
                return response()->json($answer);
            }

            $answers = Answer::all();
            return response()->json([
                'data' => $answers,
                'message' => 'Answers retrieved successfully'
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
     *     path="/api/answers",
     *     summary="Create a new answer",
     *     tags={"Answers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"survey_question_id","question_id","response_id"},
     *             @OA\Property(property="survey_question_id", type="string", format="uuid"),
     *             @OA\Property(property="question_id", type="string", format="uuid"),
     *             @OA\Property(property="response_id", type="string", format="uuid"),
     *             @OA\Property(property="answer_value", type="string", example="Sample answer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Answer created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'survey_question_id' => 'required|uuid',
                'question_id' => 'required|uuid',
                'response_id' => 'required|uuid',
                'answer_value' => 'nullable|string',
            ]);

            $answer = Answer::create([
                'answer_id' => Str::uuid(),
                'survey_question_id' => $validated['survey_question_id'],
                'question_id' => $validated['question_id'],
                'response_id' => $validated['response_id'],
                'answer_value' => $validated['answer_value'] ?? null,
                'external_id' => 'ans_' . now()->format('Y_m_') . substr(Str::uuid(), 0, 6),
            ]);

            return response()->json([
                'data' => $answer,
                'message' => 'Answer created successfully'
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
     *     path="/api/answers/{id}",
     *     summary="Update an existing answer",
     *     tags={"Answers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Answer UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="answer_value", type="string", example="Updated answer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Answer updated successfully"),
     *     @OA\Response(response=404, description="Answer not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $answer = Answer::where('answer_id', $id)->first();
            if (!$answer) {
                return response()->json(['message' => 'Answer not found'], 404);
            }

            $validated = $request->validate([
                'answer_value' => 'nullable|string',
            ]);

            $answer->update($validated);

            return response()->json([
                'data' => $answer,
                'message' => 'Answer updated successfully'
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
     *     path="/api/answers/{id}",
     *     summary="Delete an answer by UUID",
     *     tags={"Answers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Answer UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Answer deleted successfully"),
     *     @OA\Response(response=404, description="Answer not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $answer = Answer::where('answer_id', $id)->first();
            if (!$answer) {
                return response()->json(['message' => 'Answer not found'], 404);
            }

            $answer->delete();

            return response()->json(['message' => 'Answer deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
