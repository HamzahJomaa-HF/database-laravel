<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Project;
use App\Models\ProjectEmployee;
use App\Models\CredentialsEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = Employee::with(['role.moduleAccesses', 'credentials', 'projectEmployees.project'])

            ->latest()
            ->paginate(20);
        
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
    \Log::info('Store Employee Request:', $request->all());
    
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
    \Log::info('Employee created with ID: ' . $employee->employee_id);
    
    // Create credentials
    $employee->credentials()->create([
        'password_hash' => Hash::make($request->password),
         'is_active' => $request->input('is_active', true),
    ]);

    // CRITICAL: Debug project_ids before processing
    \Log::info('Project IDs from request:', [
        'has_project_ids' => $request->has('project_ids'),
        'project_ids' => $request->project_ids,
        'project_ids_type' => gettype($request->project_ids),
        'project_ids_count' => is_array($request->project_ids) ? count($request->project_ids) : 'Not an array',
    ]);

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
                    \Log::info('Created project_employee record:', [
                        'employee_id' => $employee->employee_id,
                        'project_id' => $projectId
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('old_project_ids', $request->project_ids); // Pass selected projects back
        }

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
        } else {
            // Remove all projects if none selected
            $employee->projectEmployees()->delete();
        }

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