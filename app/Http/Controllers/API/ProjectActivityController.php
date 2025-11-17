<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectActivityController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Project Activities Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/project-activities",
     *     summary="Get all project activities or a specific one by ID",
     *     tags={"Project Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional Project Activity UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project Activities retrieved successfully"),
     *     @OA\Response(response=404, description="Project Activity not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $activity = ProjectActivity::where('project_activity_id', $request->id)->first();
                if (!$activity) {
                    return response()->json(['message' => 'Project Activity not found'], 404);
                }
                return response()->json($activity);
            }

            $activities = ProjectActivity::all();
            return response()->json([
                'data' => $activities,
                'message' => 'Project Activities retrieved successfully'
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
     *     path="/api/project-activities",
     *     summary="Create a new Project Activity",
     *     tags={"Project Activities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"project_id", "activity_id"},
     *             @OA\Property(property="project_id", type="string", format="uuid", example="a2f4d8c7-8b5f-4d88-9d7c-12e456789abc"),
     *             @OA\Property(property="activity_id", type="string", format="uuid", example="b3f4a3d9-91c6-4899-bc2e-7a6b7b9b91e4")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Project Activity created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|uuid',
                'activity_id' => 'required|uuid',
            ]);

            $projectActivity = ProjectActivity::create([
                'project_activity_id' => Str::uuid(),
                'project_id' => $validated['project_id'],
                'activity_id' => $validated['activity_id'],
            ]);

            return response()->json([
                'data' => $projectActivity,
                'message' => 'Project Activity created successfully'
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
     *     path="/api/project-activities/{id}",
     *     summary="Update a Project Activity",
     *     tags={"Project Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project Activity UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="project_id", type="string", format="uuid", example="a2f4d8c7-8b5f-4d88-9d7c-12e456789abc"),
     *             @OA\Property(property="activity_id", type="string", format="uuid", example="b3f4a3d9-91c6-4899-bc2e-7a6b7b9b91e4")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Project Activity updated successfully"),
     *     @OA\Response(response=404, description="Project Activity not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $activity = ProjectActivity::where('project_activity_id', $id)->first();
            if (!$activity) {
                return response()->json(['message' => 'Project Activity not found'], 404);
            }

            $validated = $request->validate([
                'project_id' => 'sometimes|uuid',
                'activity_id' => 'sometimes|uuid',
            ]);

            $activity->update($validated);

            return response()->json([
                'data' => $activity,
                'message' => 'Project Activity updated successfully'
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
     *     path="/api/project-activities/{id}",
     *     summary="Delete a Project Activity by UUID",
     *     tags={"Project Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project Activity UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project Activity deleted successfully"),
     *     @OA\Response(response=404, description="Project Activity not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $activity = ProjectActivity::where('project_activity_id', $id)->first();
            if (!$activity) {
                return response()->json(['message' => 'Project Activity not found'], 404);
            }

            $activity->delete();

            return response()->json(['message' => 'Project Activity deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
