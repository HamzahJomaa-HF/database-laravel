<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\RpComponent;
use App\Models\RpActivity;
use App\Models\RpAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities.
     */
    public function index(Request $request)
    {
        // Start query
        $query = Activity::query();
        
        // Check if any search parameters exist
        $hasSearch = $request->anyFilled([
            'title', 'activity_type', 'venue', 
            'status', 'start_date_from', 'end_date_to'
        ]);
        
        // Apply filters
        if ($request->filled('title')) {
            $query->where(function($q) use ($request) {
                $q->where('activity_title_en', 'like', '%' . $request->title . '%')
                  ->orWhere('activity_title_ar', 'like', '%' . $request->title . '%');
            });
        }
        
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        if ($request->filled('venue')) {
            $query->where('venue', 'like', '%' . $request->venue . '%');
        }
        
        if ($request->filled('status')) {
            // Apply status logic based on dates
            $now = now();
            if ($request->status == 'upcoming') {
                $query->where('start_date', '>', $now);
            } elseif ($request->status == 'ongoing') {
                $query->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            } elseif ($request->status == 'completed') {
                $query->where('end_date', '<', $now);
            } elseif ($request->status == 'cancelled') {
                // Assuming you have an is_cancelled column
                $query->where('is_cancelled', true);
            }
        }
        
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        
        if ($request->filled('end_date_to')) {
            $query->where('end_date', '<=', $request->end_date_to);
        }
        
        // Get paginated results
        $activities = $query->orderBy('start_date', 'desc')->paginate(20);
        
        return view('activities.index', compact('activities', 'hasSearch'));
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create()
    {
        $rpComponents = RpComponent::orderBy('code')->get(['rp_components_id', 'code', 'name']);
        return view('activities.create', compact('rpComponents'));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'activity_title_en' => 'required|string|max:255',
            'activity_title_ar' => 'nullable|string|max:255',
            'activity_type' => 'required|string|max:100',
            'program' => 'required|string|max:50',
            'projects' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'content_network' => 'nullable|string',
            'rp_component_id' => 'nullable|exists:rp_components,rp_components_id',
            'rp_activities' => 'nullable|array',
            'focal_points' => 'nullable|array',
            'operational_support' => 'nullable|array',
        ]);
        
        // Convert arrays to JSON
        if (isset($validated['projects'])) {
            $validated['projects'] = json_encode($validated['projects']);
        }
        
        if (isset($validated['rp_activities'])) {
            $validated['rp_activities'] = json_encode($validated['rp_activities']);
        }
        
        if (isset($validated['focal_points'])) {
            $validated['focal_points'] = json_encode($validated['focal_points']);
        }
        
        if (isset($validated['operational_support'])) {
            $validated['operational_support'] = json_encode($validated['operational_support']);
        }
        
        // Generate a unique activity_id if not provided
        if (empty($validated['activity_id'])) {
            $validated['activity_id'] = (string) \Illuminate\Support\Str::uuid();
        }
        
        // Create the activity
        Activity::create($validated);
        
        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified activity.
     */
 public function edit($id)
{
    $activity = Activity::findOrFail($id);
    
    // Get RP Components for dropdown
    $rpComponents = RpComponent::orderBy('code')->get(['rp_components_id', 'code', 'name']);
    
    // Get selected RP activities if any
    $selectedRpActivities = json_decode($activity->rp_activities ?? '[]', true);
    
    // Get ALL RP activities grouped by action and component
    $allRpActivities = RpActivity::with(['action.component'])
        ->select('rp_activities_id', 'code', 'name', 'rp_actions_id')
        ->orderBy('code')
        ->get();
    
    // Group activities by component and action for easy filtering
    $groupedRpActivities = [];
    
    foreach ($allRpActivities as $rpActivity) {
        // Check if activity has action and action has component
        if ($rpActivity->action && $rpActivity->action->component) {
            $componentId = $rpActivity->action->component->rp_components_id;
            $actionId = $rpActivity->rp_actions_id;
            $actionName = $rpActivity->action->code . ' - ' . $rpActivity->action->name;
            
            if (!isset($groupedRpActivities[$componentId])) {
                $groupedRpActivities[$componentId] = [];
            }
            
            if (!isset($groupedRpActivities[$componentId][$actionId])) {
                $groupedRpActivities[$componentId][$actionId] = [
                    'action_name' => $actionName,
                    'activities' => []
                ];
            }
            
            $groupedRpActivities[$componentId][$actionId]['activities'][] = [
                'rp_activities_id' => $rpActivity->rp_activities_id,
                'code' => $rpActivity->code,
                'name' => $rpActivity->name,
                'full_name' => $rpActivity->code . ' - ' . $rpActivity->name
            ];
        }
    }
    
    // Convert to JSON for JavaScript
    $rpActivitiesJson = json_encode($groupedRpActivities);
    
    return view('activities.edit', compact(
        'activity', 
        'rpComponents', 
        'selectedRpActivities',
        'rpActivitiesJson'
    ));
}
    /**
     * Update the specified activity in storage.
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'activity_title_en' => 'required|string|max:255',
            'activity_title_ar' => 'nullable|string|max:255',
            'activity_type' => 'required|string|max:100',
            'program' => 'required|string|max:50',
            'projects' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'content_network' => 'nullable|string',
            'rp_component_id' => 'nullable|exists:rp_components,rp_components_id',
            'rp_activities' => 'nullable|array',
            'focal_points' => 'nullable|array',
            'operational_support' => 'nullable|array',
        ]);
        
        // Convert arrays to JSON
        if (isset($validated['projects'])) {
            $validated['projects'] = json_encode($validated['projects']);
        } else {
            $validated['projects'] = null;
        }
        
        if (isset($validated['rp_activities'])) {
            $validated['rp_activities'] = json_encode($validated['rp_activities']);
        } else {
            $validated['rp_activities'] = null;
        }
        
        if (isset($validated['focal_points'])) {
            $validated['focal_points'] = json_encode($validated['focal_points']);
        } else {
            $validated['focal_points'] = null;
        }
        
        if (isset($validated['operational_support'])) {
            $validated['operational_support'] = json_encode($validated['operational_support']);
        } else {
            $validated['operational_support'] = null;
        }
        
        // Update the activity
        $activity->update($validated);
        
        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        
        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    /**
     * Bulk delete activities.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'activity_ids' => 'required|string'
        ]);
        
        try {
            $activityIds = json_decode($request->activity_ids, true);
            
            if (empty($activityIds)) {
                return redirect()->back()->with('error', 'No activities selected.');
            }
            
            // Delete activities
            $count = Activity::whereIn('activity_id', $activityIds)->delete();
            
            return redirect()->route('activities.index')
                ->with('success', $count . ' activities deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting activities: ' . $e->getMessage());
        }
    }

    /**
     * Get RP Activities for a specific component via AJAX.
     * Returns activities grouped by their parent actions.
     */
    public function getRpActivities(Request $request): JsonResponse
    {
        $request->validate([
            'component_id' => 'required|exists:rp_components,rp_components_id'
        ]);
        
        try {
            // Method 1: Get activities through actions (since rp_activities doesn't have rp_component_id)
            // First, get actions for this component
            $actions = RpAction::where('rp_component_id', $request->component_id)
                ->with(['activities' => function($query) {
                    $query->select('rp_activities_id', 'code', 'name', 'rp_actions_id')
                          ->orderBy('code');
                }])
                ->select('rp_actions_id', 'code', 'name')
                ->orderBy('code')
                ->get();
            
            // Prepare grouped activities
            $groupedActivities = [];
            
            foreach ($actions as $action) {
                if ($action->activities->isNotEmpty()) {
                    $groupedActivities[] = [
                        'action_id' => $action->rp_actions_id,
                        'action_name' => $action->code . ' - ' . $action->name,
                        'activities' => $action->activities->map(function($activity) {
                            return [
                                'rp_activities_id' => $activity->rp_activities_id,
                                'code' => $activity->code,
                                'name' => $activity->name,
                                'full_name' => $activity->code . ' - ' . $activity->name
                            ];
                        })->toArray()
                    ];
                }
            }
            
            // If no activities found through actions, check for standalone activities
            if (empty($groupedActivities)) {
                // Get all activities (you might need to adjust this based on your business logic)
                $activities = RpActivity::select('rp_activities_id', 'code', 'name', 'rp_actions_id')
                    ->orderBy('code')
                    ->get();
                
                if ($activities->isNotEmpty()) {
                    $groupedActivities[] = [
                        'action_id' => 'general',
                        'action_name' => 'General Activities',
                        'activities' => $activities->map(function($activity) {
                            return [
                                'rp_activities_id' => $activity->rp_activities_id,
                                'code' => $activity->code,
                                'name' => $activity->name,
                                'full_name' => $activity->code . ' - ' . $activity->name
                            ];
                        })->toArray()
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $groupedActivities
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading RP activities: ' . $e->getMessage(), [
                'component_id' => $request->component_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading activities. Please check server logs.'
            ], 500);
        }
    }

    /**
     * Alternative: Get RP Activities using direct SQL join
     */
    public function getRpActivitiesAlt(Request $request): JsonResponse
    {
        $request->validate([
            'component_id' => 'required|exists:rp_components,rp_components_id'
        ]);
        
        try {
            // Using direct SQL join based on your table structure
            $activities = DB::table('rp_actions as ra')
                ->join('rp_activities as act', 'ra.rp_actions_id', '=', 'act.rp_actions_id')
                ->where('ra.rp_component_id', $request->component_id)
                ->select(
                    'act.rp_activities_id',
                    'act.code as activity_code',
                    'act.name as activity_name',
                    'ra.rp_actions_id',
                    'ra.code as action_code',
                    'ra.name as action_name',
                    DB::raw("CONCAT(act.code, ' - ', act.name) as full_activity_name"),
                    DB::raw("CONCAT(ra.code, ' - ', ra.name) as full_action_name")
                )
                ->orderBy('full_action_name')
                ->orderBy('full_activity_name')
                ->get();
            
            // Group by action
            $groupedActivities = [];
            
            foreach ($activities as $activity) {
                $actionId = $activity->rp_actions_id;
                $actionName = $activity->full_action_name;
                
                if (!isset($groupedActivities[$actionId])) {
                    $groupedActivities[$actionId] = [
                        'action_id' => $actionId,
                        'action_name' => $actionName,
                        'activities' => []
                    ];
                }
                
                $groupedActivities[$actionId]['activities'][] = [
                    'rp_activities_id' => $activity->rp_activities_id,
                    'code' => $activity->activity_code,
                    'name' => $activity->activity_name,
                    'full_name' => $activity->full_activity_name
                ];
            }
            
            // Convert to indexed array and sort
            $result = array_values($groupedActivities);
            usort($result, function($a, $b) {
                return strcmp($a['action_name'], $b['action_name']);
            });
            
            // If no results, check for activities without actions
            if (empty($result)) {
                $standaloneActivities = RpActivity::whereDoesntHave('action')
                    ->select('rp_activities_id', 'code', 'name')
                    ->orderBy('code')
                    ->get();
                
                if ($standaloneActivities->isNotEmpty()) {
                    $result[] = [
                        'action_id' => 'general',
                        'action_name' => 'General Activities',
                        'activities' => $standaloneActivities->map(function($activity) {
                            return [
                                'rp_activities_id' => $activity->rp_activities_id,
                                'code' => $activity->code,
                                'name' => $activity->name,
                                'full_name' => $activity->code . ' - ' . $activity->name
                            ];
                        })->toArray()
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}