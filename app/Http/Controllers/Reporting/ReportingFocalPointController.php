<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpFocalpoint;

class ReportingFocalPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpFocalpoint::query();
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }
        
        if ($request->has('responsibility_level')) {
            $query->where('responsibility_level', $request->responsibility_level);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('focalpoint_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }
        
        $focalpoints = $query->orderBy('name')->paginate(20);
        
        $departments = RpFocalpoint::distinct()->pluck('department')->filter()->values();
        $responsibilityLevels = RpFocalpoint::distinct()->pluck('responsibility_level')->filter()->values();
        
        return view('reporting.focalpoints.index', compact('focalpoints', 'departments', 'responsibilityLevels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = RpFocalpoint::distinct()->pluck('department')->filter()->values();
        $responsibilityLevels = ['low', 'medium', 'high', 'critical'];
        
        return view('reporting.focalpoints.create', compact('departments', 'responsibilityLevels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|uuid',
            'focalpoint_code' => 'required|string|unique:rp_focalpoints,focalpoint_code',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'responsibility_level' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $focalpoint = RpFocalpoint::create($validated);

        return redirect()->route('reporting.focalpoints.index')
            ->with('success', 'Focal point created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpFocalpoint $rpFocalpoint)
    {
        $rpFocalpoint->load(['activityAssignments.activity.action.unit.program.component']);
        
        // Get statistics
        $totalAssignments = $rpFocalpoint->activityAssignments->count();
        $activeAssignments = $rpFocalpoint->activityAssignments->where('status', 'active')->count();
        $completedAssignments = $rpFocalpoint->activityAssignments->where('status', 'completed')->count();
        
        return view('reporting.focalpoints.show', compact('rpFocalpoint', 'totalAssignments', 'activeAssignments', 'completedAssignments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpFocalpoint $rpFocalpoint)
    {
        $departments = RpFocalpoint::distinct()->pluck('department')->filter()->values();
        $responsibilityLevels = ['low', 'medium', 'high', 'critical'];
        
        return view('reporting.focalpoints.edit', compact('rpFocalpoint', 'departments', 'responsibilityLevels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpFocalpoint $rpFocalpoint)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|uuid',
            'focalpoint_code' => 'required|string|unique:rp_focalpoints,focalpoint_code,' . $rpFocalpoint->rp_focalpoints_id . ',rp_focalpoints_id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'responsibility_level' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $rpFocalpoint->update($validated);

        return redirect()->route('reporting.focalpoints.show', $rpFocalpoint)
            ->with('success', 'Focal point updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpFocalpoint $rpFocalpoint)
    {
        $rpFocalpoint->delete();

        return redirect()->route('reporting.focalpoints.index')
            ->with('success', 'Focal point deleted successfully.');
    }

    /**
     * Get focal point by code
     */
    public function getByCode($code)
    {
        $focalpoint = RpFocalpoint::where('focalpoint_code', $code)
            ->where('is_active', true)
            ->first();
        
        if (!$focalpoint) {
            return response()->json(['error' => 'Focal point not found'], 404);
        }
        
        return response()->json($focalpoint);
    }

    /**
     * Get focal points by department
     */
    public function getByDepartment($department)
    {
        $focalpoints = RpFocalpoint::where('department', $department)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($focalpoints);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $focalpoint = RpFocalpoint::findOrFail($id);
        $focalpoint->is_active = !$focalpoint->is_active;
        $focalpoint->save();
        
        $status = $focalpoint->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Focal point {$status} successfully.");
    }

    /**
     * Get focal point workload statistics
     */
    public function getWorkloadStatistics($id)
    {
        $focalpoint = RpFocalpoint::with(['activityAssignments.activity'])->findOrFail($id);
        
        $assignments = $focalpoint->activityAssignments;
        
        $statistics = [
            'total_assignments' => $assignments->count(),
            'active_assignments' => $assignments->where('status', 'active')->count(),
            'completed_assignments' => $assignments->where('status', 'completed')->count(),
            'inactive_assignments' => $assignments->where('status', 'inactive')->count(),
            'transferred_assignments' => $assignments->where('status', 'transferred')->count(),
            'roles_distribution' => $assignments->groupBy('role')->map->count(),
            'activities_by_status' => $assignments->groupBy('activity.status')->map->count()
        ];
        
        return response()->json($statistics);
    }

    /**
     * Import focal points from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls'
        ]);
        
        // TODO: Implement CSV/Excel import logic
        // You can use Laravel Excel package or manual CSV parsing
        
        return redirect()->route('reporting.focalpoints.index')
            ->with('success', 'Focal points imported successfully.');
    }

    /**
     * Export focal points
     */
    public function export(Request $request)
    {
        $focalpoints = RpFocalpoint::all();
        
        // TODO: Implement export logic (CSV, Excel, PDF)
        
        return response()->streamDownload(function() use ($focalpoints) {
            echo "Code,Name,Position,Department,Email,Phone,Responsibility Level,Status\n";
            foreach ($focalpoints as $focalpoint) {
                echo implode(',', [
                    $focalpoint->focalpoint_code,
                    $focalpoint->name,
                    $focalpoint->position,
                    $focalpoint->department,
                    $focalpoint->email,
                    $focalpoint->phone,
                    $focalpoint->responsibility_level,
                    $focalpoint->is_active ? 'Active' : 'Inactive'
                ]) . "\n";
            }
        }, 'focalpoints_' . date('Y-m-d') . '.csv');
    }

    /**
     * Get focal point assignments
     */
    public function getAssignments($id)
    {
        $focalpoint = RpFocalpoint::with([
            'activityAssignments.activity.action.unit.program.component',
            'activityAssignments.activity'
        ])->findOrFail($id);
        
        return response()->json($focalpoint->activityAssignments);
    }
}