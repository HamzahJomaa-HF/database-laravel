<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpActivityIndicator;
use App\Models\RpActivity;
use App\Models\RpIndicator;

class ReportingActivityIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpActivityIndicator::with(['activity', 'indicator']);
        
        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        
        if ($request->has('indicator_id')) {
            $query->where('indicator_id', $request->indicator_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('activity', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('indicator', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $activityIndicators = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('reporting.activity-indicators.index', compact('activityIndicators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $indicators = RpIndicator::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_indicators_id', 'name', 'code']);
        
        $statuses = ['pending', 'in_progress', 'achieved', 'partially_achieved', 'not_achieved', 'cancelled'];
        
        return view('reporting.activity-indicators.create', compact('activities', 'indicators', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'indicator_id' => 'required|uuid|exists:rp_indicators,rp_indicators_id',
            'target_value' => 'nullable|numeric',
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|string'
        ]);

        // Check if combination already exists
        $exists = RpActivityIndicator::where('activity_id', $validated['activity_id'])
            ->where('indicator_id', $validated['indicator_id'])
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'This indicator is already linked to the activity.']);
        }

        $activityIndicator = RpActivityIndicator::create($validated);

        return redirect()->route('reporting.activity-indicators.index')
            ->with('success', 'Activity-Indicator link created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpActivityIndicator $rpActivityIndicator)
    {
        $rpActivityIndicator->load(['activity.action.unit.program.component', 'indicator']);
        
        return view('reporting.activity-indicators.show', compact('rpActivityIndicator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpActivityIndicator $rpActivityIndicator)
    {
        $activities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $indicators = RpIndicator::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_indicators_id', 'name', 'code']);
        
        $statuses = ['pending', 'in_progress', 'achieved', 'partially_achieved', 'not_achieved', 'cancelled'];
        
        return view('reporting.activity-indicators.edit', compact('rpActivityIndicator', 'activities', 'indicators', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpActivityIndicator $rpActivityIndicator)
    {
        $validated = $request->validate([
            'activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'indicator_id' => 'required|uuid|exists:rp_indicators,rp_indicators_id',
            'target_value' => 'nullable|numeric',
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|string'
        ]);

        // Check if combination already exists (excluding current record)
        $exists = RpActivityIndicator::where('activity_id', $validated['activity_id'])
            ->where('indicator_id', $validated['indicator_id'])
            ->where('rp_activity_indicators_id', '!=', $rpActivityIndicator->rp_activity_indicators_id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'This indicator is already linked to the activity.']);
        }

        $rpActivityIndicator->update($validated);

        return redirect()->route('reporting.activity-indicators.show', $rpActivityIndicator)
            ->with('success', 'Activity-Indicator link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpActivityIndicator $rpActivityIndicator)
    {
        $rpActivityIndicator->delete();

        return redirect()->route('reporting.activity-indicators.index')
            ->with('success', 'Activity-Indicator link deleted successfully.');
    }

    /**
     * Get indicators by activity ID
     */
    public function getByActivity($activityId)
    {
        $activityIndicators = RpActivityIndicator::where('activity_id', $activityId)
            ->with('indicator')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($activityIndicators);
    }

    /**
     * Get activities by indicator ID
     */
    public function getByIndicator($indicatorId)
    {
        $activityIndicators = RpActivityIndicator::where('indicator_id', $indicatorId)
            ->with('activity')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($activityIndicators);
    }

    /**
     * Update achievement for activity indicator
     */
    public function updateAchievement(Request $request, $id)
    {
        $activityIndicator = RpActivityIndicator::findOrFail($id);
        
        $validated = $request->validate([
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);
        
        $activityIndicator->update($validated);
        
        return redirect()->back()
            ->with('success', 'Achievement updated successfully.');
    }

    /**
     * Calculate achievement percentage
     */
    public function calculateAchievement($id)
    {
        $activityIndicator = RpActivityIndicator::findOrFail($id);
        
        if (!$activityIndicator->target_value || $activityIndicator->target_value <= 0) {
            return response()->json(['achievement_percentage' => 0]);
        }
        
        $percentage = ($activityIndicator->achieved_value / $activityIndicator->target_value) * 100;
        
        return response()->json([
            'achievement_percentage' => $percentage,
            'target_value' => $activityIndicator->target_value,
            'achieved_value' => $activityIndicator->achieved_value,
            'status' => $activityIndicator->status
        ]);
    }

    /**
     * Get achievement summary by activity
     */
    public function getAchievementSummaryByActivity($activityId)
    {
        $activityIndicators = RpActivityIndicator::where('activity_id', $activityId)->get();
        
        $summary = [
            'total_indicators' => $activityIndicators->count(),
            'achieved' => $activityIndicators->where('status', 'achieved')->count(),
            'partially_achieved' => $activityIndicators->where('status', 'partially_achieved')->count(),
            'not_achieved' => $activityIndicators->where('status', 'not_achieved')->count(),
            'pending' => $activityIndicators->where('status', 'pending')->count(),
            'in_progress' => $activityIndicators->where('status', 'in_progress')->count(),
            'total_target_value' => $activityIndicators->sum('target_value'),
            'total_achieved_value' => $activityIndicators->sum('achieved_value'),
            'overall_achievement' => $activityIndicators->sum('target_value') > 0 
                ? ($activityIndicators->sum('achieved_value') / $activityIndicators->sum('target_value')) * 100 
                : 0
        ];
        
        return response()->json($summary);
    }
}