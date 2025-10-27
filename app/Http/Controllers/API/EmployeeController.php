<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Employees Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="Get all employees or a specific employee by ID",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional employee UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Employees retrieved successfully"),
     *     @OA\Response(response=404, description="Employee not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $employee = Employee::where('employee_id', $request->id)->first();
                if (!$employee) {
                    return response()->json(['message' => 'Employee not found'], 404);
                }
                return response()->json($employee);
            }

            $employees = Employee::all();
            return response()->json([
                'data' => $employees,
                'message' => 'Employees retrieved successfully'
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
     *     path="/api/employees",
     *     summary="Create a new employee",
     *     tags={"Employees"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email"},
     *             @OA\Property(property="project_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="role_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="employee_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone_number", type="string", example="+96170000000"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="employee_type", type="string", example="Full-time"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Employee created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'nullable|uuid',
                'role_id' => 'nullable|uuid',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'email' => 'required|string|email|max:255',
                'employee_type' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $employee = Employee::create(array_merge($validated, [
                'employee_id' => Str::uuid(),
            ]));

            return response()->json([
                'data' => $employee,
                'message' => 'Employee created successfully'
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
     *     path="/api/employees/{id}",
     *     summary="Update an existing employee",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="project_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="role_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="employee_type", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Employee updated successfully"),
     *     @OA\Response(response=404, description="Employee not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::where('employee_id', $id)->first();
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $validated = $request->validate([
                'project_id' => 'nullable|uuid',
                'role_id' => 'nullable|uuid',
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'email' => 'sometimes|string|email|max:255',
                'employee_type' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $employee->update($validated);

            return response()->json([
                'data' => $employee,
                'message' => 'Employee updated successfully'
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
     *     path="/api/employees/{id}",
     *     summary="Delete an employee by UUID",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Employee deleted successfully"),
     *     @OA\Response(response=404, description="Employee not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::where('employee_id', $id)->first();
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $employee->delete();

            return response()->json(['message' => 'Employee deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
