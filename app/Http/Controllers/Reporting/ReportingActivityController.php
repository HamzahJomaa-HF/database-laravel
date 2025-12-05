<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpActivity;
use App\Models\RpAction;
use App\Models\RpIndicator;
use App\Models\RpFocalpoint;
use App\Models\Activity;

class ReportingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpActivity::with('action.unit.program.component');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->has('needs_sync')) {
            $query->where('needs_sync', $request->needs_sync);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('activity_title_en', 'like', "%{$search}%")
                  ->orWhere('activity_title_ar', 'like', "%{$search}%");
            });
        }
        
        $activities = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('reporting.activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $actions = RpAction::where('is_active', true)
            ->with('unit.program.component')
            ->orderBy('name')
            ->get();
        
        return view('reporting.activities.create', compact('actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'action_id' => 'required|uuid|exists:rp_actions,rp_actions_id',
            'external_id' => 'nullable|uuid',
            'external_type' => 'nullable|string',
            'name' => 'required|string',
            'code' => 'required|string|unique:rp_activities,code',
            'description' => 'nullable|string',
            'activity_title_en' => 'nullable|string',
            'activity_title_ar' => 'nullable|string',
            'folder_name' => 'nullable|string',
            'content_network' => 'nullable|string',
            'activity_type' => 'nullable|string',
            'reporting_period_start' => 'nullable|date',
            'reporting_period_end' => 'nullable|date',
            'status' => 'required|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'allocated_budget' => 'nullable|numeric',
            'spent_budget' => 'nullable|numeric',
            'is_active' => 'boolean',
            'needs_sync' => 'boolean'
        ]);

        $activity = RpActivity::create($validated);

        return redirect()->route('reporting.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpActivity $rpActivity)
    {
        $rpActivity->load([
            'action.unit.program.component',
            'indicators',
            'focalpoints',
            'mappedActivities'
        ]);
        
        return view('reporting.activities.show', compact('rpActivity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpActivity $rpActivity)
    {
        $actions = RpAction::where('is_active', true)
            ->with('unit.program.component')
            ->orderBy('name')
            ->get();
        
        return view('reporting.activities.edit', compact('rpActivity', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpActivity $rpActivity)
    {
        $validated = $request->validate([
            'action_id' => 'required|uuid|exists:rp_actions,rp_actions_id',
            'external_id' => 'nullable|uuid',
            'external_type' => 'nullable|string',
            'name' => 'required|string',
            'code' => 'required|string|unique:rp_activities,code,' . $rpActivity->rp_activities_id . ',rp_activities_id',
            'description' => 'nullable|string',
            'activity_title_en' => 'nullable|string',
            'activity_title_ar' => 'nullable|string',
            'folder_name' => 'nullable|string',
            'content_network' => 'nullable|string',
            'activity_type' => 'nullable|string',
            'reporting_period_start' => 'nullable|date',
            'reporting_period_end' => 'nullable|date',
            'status' => 'required|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'allocated_budget' => 'nullable|numeric',
            'spent_budget' => 'nullable|numeric',
            'is_active' => 'boolean',
            'needs_sync' => 'boolean'
        ]);

        $rpActivity->update($validated);

        return redirect()->route('reporting.activities.show', $rpActivity)
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpActivity $rpActivity)
    {
        $rpActivity->delete();

        return redirect()->route('reporting.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    /**
     * Get activities by action ID
     */
    public function getByAction($actionId)
    {
        $activities = RpActivity::where('action_id', $actionId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($activities);
    }

    /**
     * Get activity statistics
     */
    public function getStatistics($id)
    {
        $activity = RpActivity::with([
            'indicators',
            'focalpoints',
            'mappedActivities'
        ])->findOrFail($id);
        
        $statistics = [
            'indicators_count' => $activity->indicators->count(),
            'focalpoints_count' => $activity->focalpoints->count(),
            'mapped_activities_count' => $activity->mappedActivities->count(),
            'budget_utilization' => $activity->allocated_budget > 0 ? 
                ($activity->spent_budget / $activity->allocated_budget) * 100 : 0
        ];
        
        return response()->json($statistics);
    }

    /**
     * Sync activity with main activities
     */
    public function syncWithMain($id)
    {
        $activity = RpActivity::findOrFail($id);
        
        // Your sync logic here
        
        $activity->update([
            'needs_sync' => false,
            'last_sync_at' => now()
        ]);
        
        return redirect()->back()
            ->with('success', 'Activity synced successfully.');
    }

    /**
     * Attach indicators to activity
     */
    public function attachIndicators(Request $request, $id)
    {
        $activity = RpActivity::findOrFail($id);
        
        $validated = $request->validate([
            'indicator_ids' => 'required|array',
            'indicator_ids.*' => 'uuid|exists:rp_indicators,rp_indicators_id'
        ]);
        
        $activity->indicators()->sync($validated['indicator_ids']);
        
        return redirect()->back()
            ->with('success', 'Indicators attached successfully.');
    }

    /**
     * Attach focalpoints to activity
     */
    public function attachFocalpoints(Request $request, $id)
    {
        $activity = RpActivity::findOrFail($id);
        
        $validated = $request->validate([
            'focalpoint_ids' => 'required|array',
            'focalpoint_ids.*' => 'uuid|exists:rp_focalpoints,rp_focalpoints_id'
        ]);
        
        $activity->focalpoints()->sync($validated['focalpoint_ids']);
        
        return redirect()->back()
            ->with('success', 'Focal points attached successfully.');
    }
}