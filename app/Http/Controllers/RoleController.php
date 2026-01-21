<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\ModuleAccess;
use App\Models\RoleModuleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('moduleAccesses')
            ->withCount('employees')
            ->latest()
            ->paginate(10);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        // Get all module accesses
        $moduleAccesses = ModuleAccess::all();
        
        return view('roles.create', compact('moduleAccesses'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:roles,role_name',
            'description' => 'nullable|string|max:500',
            'module_access_ids' => 'nullable|array',
            'module_access_ids.*' => 'exists:module_access,access_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        // Handle module access permissions
        if ($request->has('module_access_ids')) {
            foreach ($request->module_access_ids as $accessId) {
                RoleModuleAccess::create([
                    'role_id' => $role->role_id,
                    'access_id' => $accessId,
                    'roles_module_access_id' => Str::uuid(),
                ]);
            }
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['employees.role.moduleAccesses', 'moduleAccesses']);

        
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Load the role's current permissions
        $role->load('moduleAccesses');
        
        // Get the access_ids of current permissions
        $currentPermissions = $role->moduleAccesses->pluck('access_id')->toArray();
        
        return view('roles.edit', compact('role', 'currentPermissions'));
    }

    /**
     * Update the specified role in storage.
     */

    public function update(Request $request, Role $role)
{
    $validator = Validator::make($request->all(), [
        'role_name' => 'required|string|max:255|unique:roles,role_name,' . $role->role_id . ',role_id',
        'description' => 'nullable|string|max:500',
        'module_access_ids' => 'sometimes|array',
        'module_access_ids.*' => 'exists:module_access,access_id',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $role->update([
        'role_name' => $request->role_name,
        'description' => $request->description,
    ]);

    $accessIds = $request->input('module_access_ids', []); // always [] if none checked

    RoleModuleAccess::where('role_id', $role->role_id)->forceDelete();

    foreach ($accessIds as $accessId) {
        RoleModuleAccess::create([
            'role_id' => $role->role_id,
            'access_id' => $accessId,
            'roles_module_access_id' => (string) Str::uuid(),
        ]);
    }

    return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
}


    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role has employees
        if ($role->employees()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete role that has employees assigned. Reassign employees first.');
        }

        // Delete role-module access relationships from pivot table
        RoleModuleAccess::where('role_id', $role->role_id)->delete();
        
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Show role permissions management form.
     */
    public function permissions(Role $role)
    {
        $moduleAccesses = ModuleAccess::all();
        
        $role->load('moduleAccesses');
        
        // Get the access_ids of current permissions
        $currentPermissions = $role->moduleAccesses->pluck('access_id')->toArray();
        
        return view('roles.permissions', compact('role', 'moduleAccesses', 'currentPermissions'));
    }

    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'module_access_ids' => 'nullable|array',
            'module_access_ids.*' => 'exists:module_access,access_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Delete existing role-module access relationships
        RoleModuleAccess::where('role_id', $role->role_id)->delete();
        
        // Add new permissions if any
        if ($request->has('module_access_ids')) {
            foreach ($request->module_access_ids as $accessId) {
                RoleModuleAccess::create([
                    'role_id' => $role->role_id,
                    'access_id' => $accessId,
                    'roles_module_access_id' => Str::uuid(),
                ]);
            }
        }

        return redirect()->route('roles.show', $role)
            ->with('success', 'Role permissions updated successfully.');
    }
}