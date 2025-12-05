<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpActivityFocalpoint;
use App\Models\RpActivity;
use App\Models\RpFocalpoint;

class ReportingActivityFocalPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpActivityFocalpoint::with(['activity', 'focalpoint']);
        
        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        
        if ($request->has('focalpoint_id')) {
            $query->where('focalpoint_id', $request->focalpoint_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('activity', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('focalpoint', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $activityFocalpoints = $query->orderBy('assigned_date', 'desc')->paginate(20);
        
        return view('reporting.activity-focalpoints.index', compact('activityFocalpoints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $focalpoints = RpFocalpoint::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_focalpoints_id', 'name', 'email', 'position']);
        
        $roles = ['focal_point', 'coordinator', 'supervisor', 'manager', 'technical_lead', 'monitoring_officer', 'evaluator', 'other'];
        
        $statuses = ['active', 'inactive', 'completed', 'transferred'];
        
        return view('reporting.activity-focalpoints.create', compact('activities', 'focalpoints', 'roles', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'focalpoint_id' => 'required|uuid|exists:rp_focalpoints,rp_focalpoints_id',
            'role' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'assigned_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:assigned_date',
            'status' => 'required|string'
        ]);

        // Check if combination already exists
        $exists = RpActivityFocalpoint::where('activity_id', $validated['activity_id'])
            ->where('focalpoint_id', $validated['focalpoint_id'])
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'This focal point is already assigned to the activity.']);
        }

        $activityFocalpoint = RpActivityFocalpoint::create($validated);

        return redirect()->route('reporting.activity-focalpoints.index')
            ->with('success', 'Activity-Focal Point assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpActivityFocalpoint $rpActivityFocalpoint)
    {
        $rpActivityFocalpoint->load(['activity.action.unit.program.component', 'focalpoint']);
        
        return view('reporting.activity-focalpoints.show', compact('rpActivityFocalpoint'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpActivityFocalpoint $rpActivityFocalpoint)
    {
        $activities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $focalpoints = RpFocalpoint::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_focalpoints_id', 'name', 'email', 'position']);
        
        $roles = ['focal_point', 'coordinator', 'supervisor', 'manager', 'technical_lead', 'monitoring_officer', 'evaluator', 'other'];
        
        $statuses = ['active', 'inactive', 'completed', 'transferred'];
        
        return view('reporting.activity-focalpoints.edit', compact('rpActivityFocalpoint', 'activities', 'focalpoints', 'roles', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpActivityFocalpoint $rpActivityFocalpoint)
    {
        $validated = $request->validate([
            'activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'focalpoint_id' => 'required|uuid|exists:rp_focalpoints,rp_focalpoints_id',
            'role' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'assigned_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:assigned_date',
            'status' => 'required|string'
        ]);

        // Check if combination already exists (excluding current record)
        $exists = RpActivityFocalpoint::where('activity_id', $validated['activity_id'])
            ->where('focalpoint_id', $validated['focalpoint_id'])
            ->where('rp_activity_focalpoints_id', '!=', $rpActivityFocalpoint->rp_activity_focalpoints_id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'This focal point is already assigned to the activity.']);
        }

        $rpActivityFocalpoint->update($validated);

        return redirect()->route('reporting.activity-focalpoints.show', $rpActivityFocalpoint)
            ->with('success', 'Activity-Focal Point assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpActivityFocalpoint $rpActivityFocalpoint)
    {
        $rpActivityFocalpoint->delete();

        return redirect()->route('reporting.activity-focalpoints.index')
            ->with('success', 'Activity-Focal Point assignment deleted successfully.');
    }

    /**
     * Get focal points by activity ID
     */
    public function getByActivity($activityId)
    {
        $activityFocalpoints = RpActivityFocalpoint::where('activity_id', $activityId)
            ->with('focalpoint')
            ->where('status', 'active')
            ->orderBy('assigned_date', 'desc')
            ->get();
        
        return response()->json($activityFocalpoints);
    }

    /**
     * Get activities by focal point ID
     */
    public function getByFocalPoint($focalpointId)
    {
        $activityFocalpoints = RpActivityFocalpoint::where('focalpoint_id', $focalpointId)
            ->with('activity')
            ->where('status', 'active')
            ->orderBy('assigned_date', 'desc')
            ->get();
        
        return response()->json($activityFocalpoints);
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, $id)
    {
        $activityFocalpoint = RpActivityFocalpoint::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|string',
            'end_date' => 'nullable|date',
            'responsibilities' => 'nullable|string'
        ]);
        
        $activityFocalpoint->update($validated);
        
        return redirect()->back()
            ->with('success', 'Assignment status updated successfully.');
    }

    /**
     * Get assignment summary by activity
     */
    public function getSummaryByActivity($activityId)
    {
        $activityFocalpoints = RpActivityFocalpoint::where('activity_id', $activityId)->get();
        
        $summary = [
            'total_assignments' => $activityFocalpoints->count(),
            'active' => $activityFocalpoints->where('status', 'active')->count(),
            'inactive' => $activityFocalpoints->where('status', 'inactive')->count(),
            'completed' => $activityFocalpoints->where('status', 'completed')->count(),
            'transferred' => $activityFocalpoints->where('status', 'transferred')->count(),
            'roles_summary' => $activityFocalpoints->groupBy('role')->map->count()
        ];
        
        return response()->json($summary);
    }

    /**
     * Get focal point workload
     */
    public function getFocalPointWorkload($focalpointId)
    {
        $activityFocalpoints = RpActivityFocalpoint::where('focalpoint_id', $focalpointId)
            ->with('activity')
            ->get();
        
        $workload = [
            'total_assignments' => $activityFocalpoints->count(),
            'active_assignments' => $activityFocalpoints->where('status', 'active')->count(),
            'roles' => $activityFocalpoints->groupBy('role')->map->count(),
            'activities_by_status' => $activityFocalpoints->groupBy('activity.status')->map->count()
        ];
        
        return response()->json($workload);
    }
}