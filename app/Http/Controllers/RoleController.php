<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\ModuleAccess;
use App\Models\RoleModuleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Add this import

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
   public function index()
{
    // Don't specify columns in the relationship - let Laravel handle it
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
        // Get all module accesses grouped by module
        $modules = ModuleAccess::all()->groupBy('module');
        
        return view('roles.create', compact('modules'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:roles,role_name',
            'description' => 'nullable|string|max:500',
            'module_access' => 'nullable|array',
            'module_access.*' => 'in:none,view,create,edit,delete,manage,full', // Add validation
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
        if ($request->has('module_access')) {
            foreach ($request->module_access as $module => $accessLevel) {
                if ($accessLevel !== 'none') {
                    // Find or create module access WITH UUID
                    $moduleAccess = ModuleAccess::firstOrCreate(
                        [
                            'module' => $module,
                            'access_level' => $accessLevel,
                        ],
                        [
                            'access_id' => Str::uuid(), // Add UUID for new records
                        ]
                    );

                    // Create role-module access relationship WITH UUID
                    RoleModuleAccess::create([
                        'role_id' => $role->role_id,
                        'access_id' => $moduleAccess->access_id,
                        'roles_module_access_id' => Str::uuid(), // Add UUID
                    ]);
                }
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
        // Load the correct relationship name
        $role->load(['employees', 'moduleAccesses']); // Remove .moduleAccess
        
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $modules = ModuleAccess::all()->groupBy('module');
        
        // Load the correct relationship
        $role->load('moduleAccesses');
        
        return view('roles.edit', compact('role', 'modules'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:roles,role_name,' . $role->role_id . ',role_id',
            'description' => 'nullable|string|max:500',
            'module_access' => 'nullable|array',
            'module_access.*' => 'in:none,view,create,edit,delete,manage,full',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        // Update module access permissions
        if ($request->has('module_access')) {
            // Delete existing module access
            RoleModuleAccess::where('role_id', $role->role_id)->delete();
            
            foreach ($request->module_access as $module => $accessLevel) {
                if ($accessLevel !== 'none') {
                    // Find or create module access WITH UUID
                    $moduleAccess = ModuleAccess::firstOrCreate(
                        [
                            'module' => $module,
                            'access_level' => $accessLevel,
                        ],
                        [
                            'access_id' => Str::uuid(), // Add UUID for new records
                        ]
                    );

                    // Create role-module access relationship WITH UUID
                    RoleModuleAccess::create([
                        'role_id' => $role->role_id,
                        'access_id' => $moduleAccess->access_id,
                        'roles_module_access_id' => Str::uuid(), // Add UUID
                    ]);
                }
            }
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
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

        // Delete role-module access relationships
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
        $modules = ModuleAccess::all()->groupBy('module');
        
        // Load the correct relationship
        $role->load('moduleAccesses');
        
        return view('roles.permissions', compact('role', 'modules'));
    }

    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'module_access' => 'required|array',
            'module_access.*' => 'required|string|in:none,view,create,edit,delete,manage,full',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Delete existing module access
        RoleModuleAccess::where('role_id', $role->role_id)->delete();
        
        foreach ($request->module_access as $module => $accessLevel) {
            if ($accessLevel !== 'none') {
                // Find or create module access WITH UUID
                $moduleAccess = ModuleAccess::firstOrCreate(
                    [
                        'module' => $module,
                        'access_level' => $accessLevel,
                    ],
                    [
                        'access_id' => Str::uuid(), // Add UUID for new records
                    ]
                );

                // Create role-module access relationship WITH UUID
                RoleModuleAccess::create([
                    'role_id' => $role->role_id,
                    'access_id' => $moduleAccess->access_id,
                    'roles_module_access_id' => Str::uuid(), // Add UUID
                ]);
            }
        }

        return redirect()->route('roles.show', $role)
            ->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Get roles for API (AJAX requests).
     */
    public function apiIndex()
    {
        $roles = Role::select('role_id', 'role_name')
            ->orderBy('role_name')
            ->get();
        
        return response()->json($roles);
    }

    /**
     * Get role permissions for API.
     */
    public function apiPermissions(Role $role)
    {
        $permissions = $role->moduleAccesses()
            ->select('module', 'access_level')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->module => $item->access_level];
            });
        
        return response()->json($permissions);
    }
}