<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectEmployeeController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Project Employees Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/project-employees",
     *     summary="Get all project employees or a specific one by ID",
     *     tags={"Project Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional UUID to fetch a specific project employee",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project Employees retrieved successfully"),
     *     @OA\Response(response=404, description="Project Employee not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $projectEmployee = ProjectEmployee::where('project_employee_id', $request->id)->first();
                if (!$projectEmployee) {
                    return response()->json(['message' => 'Project Employee not found'], 404);
                }
                return response()->json($projectEmployee);
            }

            $projectEmployees = ProjectEmployee::all();
            return response()->json([
                'data' => $projectEmployees,
                'message' => 'Project Employees retrieved successfully'
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
     *     path="/api/project-employees",
     *     summary="Create a new project employee",
     *     tags={"Project Employees"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"program_id", "employee_id"},
     *             @OA\Property(property="program_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="employee_id", type="string", format="uuid", example="321e6547-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="description", type="string", example="Project coordination duties")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Project Employee created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'program_id' => 'required|uuid|exists:programs,program_id',
                'employee_id' => 'required|uuid|exists:employees,employee_id',
                'description' => 'nullable|string'
            ]);

            $projectEmployee = ProjectEmployee::create([
                'project_employee_id' => Str::uuid(),
                'program_id' => $validated['program_id'],
                'employee_id' => $validated['employee_id'],
                'description' => $validated['description'] ?? null,
            ]);

            return response()->json([
                'data' => $projectEmployee,
                'message' => 'Project Employee created successfully'
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
     *     path="/api/project-employees/{id}",
     *     summary="Update a project employee",
     *     tags={"Project Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Project Employee UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="program_id", type="string", format="uuid"),
     *             @OA\Property(property="employee_id", type="string", format="uuid"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Project Employee updated successfully"),
     *     @OA\Response(response=404, description="Project Employee not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $projectEmployee = ProjectEmployee::where('project_employee_id', $id)->first();
            if (!$projectEmployee) {
                return response()->json(['message' => 'Project Employee not found'], 404);
            }

            $validated = $request->validate([
                'program_id' => 'sometimes|uuid|exists:programs,program_id',
                'employee_id' => 'sometimes|uuid|exists:employees,employee_id',
                'description' => 'nullable|string'
            ]);

            $projectEmployee->update($validated);

            return response()->json([
                'data' => $projectEmployee,
                'message' => 'Project Employee updated successfully'
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
     *     path="/api/project-employees/{id}",
     *     summary="Delete a project employee",
     *     tags={"Project Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of project employee to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Project Employee deleted successfully"),
     *     @OA\Response(response=404, description="Project Employee not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $projectEmployee = ProjectEmployee::where('project_employee_id', $id)->first();
            if (!$projectEmployee) {
                return response()->json(['message' => 'Project Employee not found'], 404);
            }

            $projectEmployee->delete();

            return response()->json(['message' => 'Project Employee deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
