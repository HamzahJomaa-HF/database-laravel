<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Project;
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
        $employees = Employee::with(['role', 'credentials'])
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
        $projects = Project::all(); // Make sure you have Project model
        
        return view('employees.create', compact('roles', 'projects'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:255',
            'employee_type' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'role_id' => 'required|exists:roles,role_id',
            'project_id' => 'nullable|exists:projects,project_id',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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
            'project_id' => $request->project_id,
        ]);

        // Create credentials
        $employee->credentials()->create([
            'password_hash' => Hash::make($request->password),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['role', 'credentials', 'project']);
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $roles = Role::all();
        $projects = Project::all();
        
        return view('employees.edit', compact('employee', 'roles', 'projects'));
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
            'project_id' => 'nullable|exists:projects,project_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $employee->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'employee_type' => $request->employee_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'role_id' => $request->role_id,
            'project_id' => $request->project_id,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee from storage (soft delete).
     */
    public function destroy(Employee $employee)
    {
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
            ->with(['role', 'credentials'])
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
        
        // Delete credentials first
        if ($employee->credentials) {
            $employee->credentials->forceDelete();
        }
        
        $employee->forceDelete();
        
        return redirect()->route('employees.trashed')
            ->with('success', 'Employee permanently deleted.');
    }
}