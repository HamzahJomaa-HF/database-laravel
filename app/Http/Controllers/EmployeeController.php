<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Project;
use App\Models\ProjectEmployee;
use App\Models\CredentialsEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $employees = Employee::with(['role.moduleAccesses', 'credentials', 'projectEmployees.project'])

            ->latest()
            ->paginate($perPage);
        
        $roles = Role::all();
        
        return view('employees.index', compact('employees', 'roles'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $roles = Role::all();
        $projects = Project::all();
        
        return view('employees.create', compact('roles', 'projects'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
{
    // Debug: Log the request data
    
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'phone_number' => 'nullable|string|max:255',
        'employee_type' => 'nullable|string|max:255',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'role_id' => 'required|exists:roles,role_id',
        // Make sure these are correct
        'project_ids' => 'nullable|array',
        'project_ids.*' => 'nullable|exists:projects,project_id',
        'password' => 'required|string|min:8|confirmed',
        'is_active' => 'boolean',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', $validator->errors()->toArray());
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('old_project_ids', $request->project_ids); // ADD THIS LINE
    }

    // Create employee
    $employee = Employee::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'employee_type' => $request->employee_type,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'role_id' => $request->role_id,
    ]);

    // Debug: Log the employee creation
    
    // Create credentials
    $employee->credentials()->create([
        'password_hash' => Hash::make($request->password),
         'is_active' => $request->input('is_active', true),
    ]);

    // CRITICAL: Debug project_ids before processing
   

    // Create project associations - FIXED VERSION
    if ($request->filled('project_ids') && is_array($request->project_ids)) {
        foreach ($request->project_ids as $projectId) {
            // Validate projectId is not empty
            if (!empty($projectId)) {
                try {
                    ProjectEmployee::create([
                        'employee_id' => $employee->employee_id,
                        'project_id' => $projectId,
                    ]);
                   
                } catch (\Exception $e) {
                    \Log::error('Failed to create project_employee:', [
                        'error' => $e->getMessage(),
                        'employee_id' => $employee->employee_id,
                        'project_id' => $projectId
                    ]);
                }
            }
        }
    } else {
        \Log::warning('No project_ids found or not an array');
    }

    return redirect()->route('employees.index')
        ->with('success', 'Employee created successfully.');
}

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
     $employee->load(['role.moduleAccesses', 'credentials', 'projectEmployees.project']);
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $roles = Role::all();
        $projects = Project::all();
        
        // Get current employee's project IDs from pivot table (multiple projects)
        $currentProjectIds = $employee->projectEmployees()->pluck('project_id')->toArray();
        
        return view('employees.edit', compact('employee', 'roles', 'projects', 'currentProjectIds'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        Log::info('[EMPLOYEE-UPDATE] Request received', [
            'employee_id' => $employee->employee_id,
            'current_role_id' => $employee->role_id,
            'submitted_role_id' => $request->role_id,
            'input' => $request->except(['password', 'password_confirmation', '_token']),
        ]);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->employee_id . ',employee_id',
            'phone_number' => 'nullable|string|max:255',
            'employee_type' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'role_id' => 'required|exists:roles,role_id',
            // CHANGE: project_ids as array for multiple projects
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'exists:projects,project_id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('[EMPLOYEE-UPDATE] Validation failed', [
                'employee_id' => $employee->employee_id,
                'errors' => $validator->errors()->toArray(),
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('old_project_ids', $request->project_ids); // Pass selected projects back
        }

        try {
            // Update employee
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'employee_type' => $request->employee_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'role_id' => $request->role_id,
            ]);

            Log::info('[EMPLOYEE-UPDATE] Employee row updated', [
                'employee_id' => $employee->employee_id,
                'role_id_after' => $employee->fresh()->role_id,
            ]);

            // Update password if a new one was provided
            if ($request->filled('password')) {
                if ($employee->credentials) {
                    $employee->credentials->update([
                        'password_hash' => Hash::make($request->password),
                    ]);
                } else {
                    $employee->credentials()->create([
                        'password_hash' => Hash::make($request->password),
                        'is_active' => true,
                    ]);
                }
                Log::info('[EMPLOYEE-UPDATE] Password updated', ['employee_id' => $employee->employee_id]);
            }

            // SYNC PROJECT ASSOCIATIONS (Add/Remove multiple projects)
            if ($request->has('project_ids')) {
                // Get current project IDs
                $currentProjectIds = $employee->projectEmployees()->pluck('project_id')->toArray();
                $newProjectIds = $request->project_ids;

                // Find projects to add
                $projectsToAdd = array_diff($newProjectIds, $currentProjectIds);

                // Find projects to remove
                $projectsToRemove = array_diff($currentProjectIds, $newProjectIds);

                // Add new projects
                foreach ($projectsToAdd as $projectId) {
                    ProjectEmployee::create([
                        'employee_id' => $employee->employee_id,
                        'project_id' => $projectId,
                    ]);
                }

                // Remove old projects
                ProjectEmployee::where('employee_id', $employee->employee_id)
                    ->whereIn('project_id', $projectsToRemove)
                    ->delete();

                Log::info('[EMPLOYEE-UPDATE] Project associations synced', [
                    'employee_id' => $employee->employee_id,
                    'added' => array_values($projectsToAdd),
                    'removed' => array_values($projectsToRemove),
                ]);
            } else {
                // Remove all projects if none selected
                $employee->projectEmployees()->delete();
                Log::info('[EMPLOYEE-UPDATE] No project_ids submitted - cleared all project associations', [
                    'employee_id' => $employee->employee_id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[EMPLOYEE-UPDATE] Exception while updating employee', [
                'employee_id' => $employee->employee_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        Log::info('[EMPLOYEE-UPDATE] Success - redirecting to employees.index', [
            'employee_id' => $employee->employee_id,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }
// In your EmployeeController.php, add this method:

/**
 * Toggle employee active status.
 */
public function toggleStatus($id)
{
    $employee = Employee::findOrFail($id);
    
    // Check if credentials exist
    if ($employee->credentials) {
        // Toggle the is_active status
        $employee->credentials->update([
            'is_active' => !$employee->credentials->is_active
        ]);
        
        $status = $employee->credentials->is_active ? 'activated' : 'deactivated';
        return redirect()->route('employees.index')
            ->with('success', "Employee account {$status} successfully.");
    } else {
        // Create credentials if they don't exist
        $employee->credentials()->create([
            'password_hash' => Hash::make('password123'), // Default password
            'is_active' => true
        ]);
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee credentials created and account activated.');
    }
}
    /**
     * Remove the specified employee from storage (soft delete).
     */
    public function destroy(Employee $employee)
    {
        // Delete project associations first
        $employee->projectEmployees()->delete();
        
        // Delete the employee
        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Activate employee account.
     */
    public function activate(Employee $employee)
    {
        if ($employee->credentials) {
            $employee->credentials->update(['is_active' => true]);
        } else {
            $employee->credentials()->create(['is_active' => true, 'password_hash' => Hash::make('password')]);
        }
        
        return redirect()->back()
            ->with('success', 'Employee account activated.');
    }

    /**
     * Deactivate employee account.
     */
    public function deactivate(Employee $employee)
    {
        if ($employee->credentials) {
            $employee->credentials->update(['is_active' => false]);
        }
        
        return redirect()->back()
            ->with('success', 'Employee account deactivated.');
    }

    /**
     * Display a listing of trashed employees.
     */
    public function trashed()
    {
      $employees = Employee::onlyTrashed()
        ->with(['role.moduleAccesses', 'credentials', 'projectEmployees.project'])
            ->latest()
            ->paginate(20);
        
        return view('employees.trashed', compact('employees'));
    }

    /**
     * Restore a soft deleted employee.
     */
    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee restored successfully.');
    }

    /**
     * Permanently delete an employee.
     */
    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        
        // Delete project associations first
        $employee->projectEmployees()->forceDelete();
        
        // Delete credentials
        if ($employee->credentials) {
            $employee->credentials->forceDelete();
        }
        
        // Delete the employee
        $employee->forceDelete();
        
        return redirect()->route('employees.trashed')
            ->with('success', 'Employee permanently deleted.');
    }
}