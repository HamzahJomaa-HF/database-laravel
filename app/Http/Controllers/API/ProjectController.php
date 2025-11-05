<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Project Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/projects",
     *     summary="Get all projects or a specific project by ID",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional project UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Projects retrieved successfully"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $project = Project::where('project_id', $request->id)->first();
                if (!$project) {
                    return response()->json(['message' => 'Project not found'], 404);
                }
                return response()->json($project);
            }

            $projects = Project::all();
            return response()->json([
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
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
     *     path="/api/projects",
     *     summary="Create a new project",
     *     tags={"Projects"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"program_id"},
     *             @OA\Property(property="name", type="string", example="Project 1"),
     *             @OA\Property(property="folder_name", type="string", example="Project_1_Folder"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-22"),
     *             @OA\Property(property="program_id", type="string", format="uuid"),
     *             @OA\Property(property="parent_project_id", type="string", format="uuid"),
     *             @OA\Property(property="project_type", type="string", example="Research"),
     *             @OA\Property(property="project_group", type="string", example="Group A")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Project created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'folder_name' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'program_id' => 'required|uuid|exists:programs,program_id',
                'parent_project_id' => 'nullable|uuid|exists:projects,project_id',
                'project_type' => 'nullable|string|max:255',
                'project_group' => 'nullable|string|max:255',
            ]);

            // ✅ Use mass assignment (the model boot() handles UUID + external_id)
            $project = Project::create($validated);

            return response()->json([
                'data' => $project,
                'message' => 'Project created successfully'
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
     *     path="/api/projects/{id}",
     *     summary="Update an existing project",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="folder_name", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="program_id", type="string", format="uuid"),
     *             @OA\Property(property="parent_project_id", type="string", format="uuid"),
     *             @OA\Property(property="project_type", type="string"),
     *             @OA\Property(property="project_group", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Project updated successfully"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $project = Project::where('project_id', $id)->first();
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'folder_name' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'program_id' => 'sometimes|uuid|exists:programs,program_id',
                'parent_project_id' => 'nullable|uuid|exists:projects,project_id',
                'project_type' => 'nullable|string|max:255',
                'project_group' => 'nullable|string|max:255',
            ]);

            $project->fill($validated);

            if (isset($validated['project_type']) && $validated['project_type'] !== $project->getOriginal('project_type')) {
                $project->external_id = 'proj_' . date('Y_m') . '_' . strtolower($validated['project_type']) . '_' . substr(Str::uuid(), 0, 4);
            }

            $project->save();

            return response()->json([
                'data' => $project,
                'message' => 'Project updated successfully'
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
     *     path="/api/projects/{id}",
     *     summary="Delete a project by UUID",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project deleted successfully"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $project = Project::where('project_id', $id)->first();
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }

            $project->delete();

            return response()->json(['message' => 'Project deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
