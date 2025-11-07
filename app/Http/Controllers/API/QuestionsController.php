<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionsController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Questions Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/questions",
     *     summary="Get all questions or a specific question by ID",
     *     tags={"Questions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional question UUID to fetch a specific question",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Questions retrieved successfully"),
     *     @OA\Response(response=404, description="Question not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $question = Question::where('question_id', $request->id)->first();
                if (!$question) {
                    return response()->json(['message' => 'Question not found'], 404);
                }
                return response()->json($question);
            }

            $questions = Question::all();
            return response()->json([
                'data' => $questions,
                'message' => 'Questions retrieved successfully'
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
     *     path="/api/questions",
     *     summary="Create a new question",
     *     tags={"Questions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question_type", "question_name"},
     *             @OA\Property(property="survey_id", type="string", format="uuid", example="c53fbb2e-8b7c-4a2e-bbb0-fd1234abcd56"),
     *             @OA\Property(property="question_type", type="string", example="multiple_choice"),
     *             @OA\Property(property="question_name", type="string", example="What is your favorite color?")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Question created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'survey_id' => 'nullable|uuid',
                'question_type' => 'required|string|max:255',
                'question_name' => 'required|string|max:255',
            ]);

            $question = Question::create([
                'question_id' => Str::uuid(),
                'survey_id' => $validated['survey_id'] ?? null,
                'question_type' => $validated['question_type'],
                'question_name' => $validated['question_name'],
            ]);

            return response()->json([
                'data' => $question,
                'message' => 'Question created successfully'
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
     *     path="/api/questions/{id}",
     *     summary="Update an existing question",
     *     tags={"Questions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Question UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="survey_id", type="string", format="uuid", example="c53fbb2e-8b7c-4a2e-bbb0-fd1234abcd56"),
     *             @OA\Property(property="question_type", type="string", example="single_choice"),
     *             @OA\Property(property="question_name", type="string", example="Updated question name")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Question updated successfully"),
     *     @OA\Response(response=404, description="Question not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $question = Question::where('question_id', $id)->first();
            if (!$question) {
                return response()->json(['message' => 'Question not found'], 404);
            }

            $validated = $request->validate([
                'survey_id' => 'nullable|uuid',
                'question_type' => 'sometimes|string|max:255',
                'question_name' => 'sometimes|string|max:255',
            ]);

            $question->update($validated);

            return response()->json([
                'data' => $question,
                'message' => 'Question updated successfully'
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
     *     path="/api/questions/{id}",
     *     summary="Delete a question by UUID",
     *     tags={"Questions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Question UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Question deleted successfully"),
     *     @OA\Response(response=404, description="Question not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $question = Question::where('question_id', $id)->first();
            if (!$question) {
                return response()->json(['message' => 'Question not found'], 404);
            }

            $question->delete();

            return response()->json(['message' => 'Question deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
