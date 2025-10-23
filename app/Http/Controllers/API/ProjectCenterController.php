<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCenterController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Project Centers Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/project-centers",
     *     summary="Get all project centers or a specific project center by ID",
     *     tags={"ProjectCenters"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional project center UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project centers retrieved successfully"),
     *     @OA\Response(response=404, description="Project center not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $projectCenter = ProjectCenter::where('project_center_id', $request->id)->first();
                if (!$projectCenter) {
                    return response()->json(['message' => 'Project center not found'], 404);
                }
                return response()->json($projectCenter);
            }

            $projectCenters = ProjectCenter::all();
            return response()->json([
                'data' => $projectCenters,
                'message' => 'Project centers retrieved successfully'
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
     *     path="/api/project-centers",
     *     summary="Create a new project center",
     *     tags={"ProjectCenters"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"program_id"},
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-22"),
     *             @OA\Property(property="program_id", type="string", format="uuid", example="a1b2c3d4-5678-90ab-cdef-1234567890ab"),
     *             @OA\Property(property="parent_project_center_id", type="string", format="uuid", example="b2c3d4e5-6789-0abc-def1-234567890bcd"),
     *             @OA\Property(property="project_type", type="string", example="Research"),
     *             @OA\Property(property="project_group", type="string", example="Group A")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Project center created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'program_id' => 'required|uuid|exists:programs,program_id',
                'parent_project_center_id' => 'nullable|uuid|exists:project_centers,project_center_id',
                'project_type' => 'nullable|string|max:255',
                'project_group' => 'nullable|string|max:255',
            ]);

            $projectCenter = ProjectCenter::create(array_merge($validated, [
                'project_center_id' => Str::uuid(),
                'external_id' => 'pc_' . date('Y_m') . '_' . strtolower($validated['project_type'] ?? 'general') . '_' . substr(Str::uuid(), 0, 4),
            ]));

            return response()->json([
                'data' => $projectCenter,
                'message' => 'Project center created successfully'
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
     *     path="/api/project-centers/{id}",
     *     summary="Update an existing project center",
     *     tags={"ProjectCenters"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project center UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-21"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-25"),
     *             @OA\Property(property="program_id", type="string", format="uuid", example="a1b2c3d4-5678-90ab-cdef-1234567890ab"),
     *             @OA\Property(property="parent_project_center_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="project_type", type="string", example="Seminar"),
     *             @OA\Property(property="project_group", type="string", example="Group B")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Project center updated successfully"),
     *     @OA\Response(response=404, description="Project center not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $projectCenter = ProjectCenter::where('project_center_id', $id)->first();
            if (!$projectCenter) {
                return response()->json(['message' => 'Project center not found'], 404);
            }

            $validated = $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'program_id' => 'sometimes|uuid|exists:programs,program_id',
                'parent_project_center_id' => 'nullable|uuid|exists:project_centers,project_center_id',
                'project_type' => 'nullable|string|max:255',
                'project_group' => 'nullable|string|max:255',
            ]);

            $projectCenter->fill($validated);

            // Update external_id if project_type changes
            if (isset($validated['project_type']) && $validated['project_type'] !== $projectCenter->getOriginal('project_type')) {
                $projectCenter->external_id = 'pc_' . date('Y_m') . '_' . strtolower($validated['project_type']) . '_' . substr(Str::uuid(), 0, 4);
            }

            $projectCenter->save();

            return response()->json([
                'data' => $projectCenter,
                'message' => 'Project center updated successfully'
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
     *     path="/api/project-centers/{id}",
     *     summary="Delete a project center by UUID",
     *     tags={"ProjectCenters"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project center UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project center deleted successfully"),
     *     @OA\Response(response=404, description="Project center not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $projectCenter = ProjectCenter::where('project_center_id', $id)->first();
            if (!$projectCenter) {
                return response()->json(['message' => 'Project center not found'], 404);
            }

            $projectCenter->delete();

            return response()->json(['message' => 'Project center deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
