<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\RpComponent;
use App\Models\RpActivity;
use App\Models\RpAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        // Get RP Components for dropdown (limit to 5 as requested in first code)
        $rpComponents = RpComponent::whereNull('deleted_at')
            ->orderBy('code')
            ->limit(5)
            ->get(['rp_components_id', 'code', 'name']);
        
        // Get selected RP activities if any
        $selectedRpActivities = json_decode($activity->rp_activities ?? '[]', true);
        
        // Initialize selected component
        $selectedComponent = null;
        
        // Try to find the component from reporting activities or existing rp_component_id
        if (!empty($activity->rp_component_id)) {
            $selectedComponent = RpComponent::where('rp_components_id', $activity->rp_component_id)->first();
        }
        
        return view('activities.edit', compact(
            'activity', 
            'rpComponents', 
            'selectedRpActivities',
            'selectedComponent'
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
     * Get RP Activities by Component ID (AJAX endpoint)
     * Using Eloquent relationships from first code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
  public function getRPActivities(Request $request)
{
    $request->validate([
        'component_id' => 'required|string'
    ]);
    
    $componentId = $request->component_id;
    
    Log::info('getRPActivities called', ['component_id' => $componentId]);
    
    try {
        Log::info('Querying database for component', ['component_id' => $componentId]);
        
        // Simple query for testing
        $activities = RpActivity::whereNull('deleted_at')
            ->orderBy('code')
            ->limit(5)
            ->get(['rp_activities_id', 'code', 'name', 'rp_actions_id'])
            ->map(function($activity) {
                return [
                    'rp_activities_id' => $activity->rp_activities_id,
                    'name' => $activity->name,
                    'code' => $activity->code,
                    'rp_action_id' => $activity->rp_actions_id,
                    'action_name' => 'Action ' . $activity->rp_actions_id
                ];
            });
        
        Log::info('Found activities', [
            'component_id' => $componentId,
            'count' => $activities->count()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $activities,
            'debug' => [
                'component_id' => $componentId,
                'count' => $activities->count()
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error loading RP activities', [
            'component_id' => $componentId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error loading activities: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get RP Actions grouped by Component ID
     * Returns actions with their activities as sub-items
     * From first code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRPActionsWithActivities(Request $request)
    {
        $request->validate([
            'component_id' => 'required|string'
        ]);
        
        $componentId = $request->component_id;
        
        Log::info('getRPActionsWithActivities called', ['component_id' => $componentId]);
        
        // For testing
        if (strpos($componentId, 'test-') === 0) {
            return response()->json([
                'success' => true,
                'data' => $this->getTestActionsWithActivities()
            ]);
        }
        
        try {
            // Get the component with its full hierarchy from first code
            $component = RpComponent::with([
                'programs.units.actions.activities' => function($query) {
                    $query->whereNull('deleted_at')
                          ->orderBy('code')
                          ->select(['rp_activities_id', 'rp_actions_id', 'name', 'code']);
                }
            ])
            ->where('rp_components_id', $componentId)
            ->whereNull('deleted_at')
            ->first();
            
            if (!$component) {
                return response()->json([
                    'success' => false,
                    'message' => 'Component not found'
                ], 404);
            }
            
            // Structure the data: Group by Action
            $actionsData = [];
            
            foreach ($component->programs as $program) {
                foreach ($program->units as $unit) {
                    foreach ($unit->actions as $action) {
                        if ($action->activities->isNotEmpty()) {
                            $actionsData[] = [
                                'action_id' => $action->rp_actions_id,
                                'action_name' => $action->name,
                                'action_code' => $action->code,
                                'activities' => $action->activities->map(function($activity) {
                                    return [
                                        'rp_activities_id' => $activity->rp_activities_id,
                                        'name' => $activity->name,
                                        'code' => $activity->code,
                                        'rp_action_id' => $activity->rp_actions_id,
                                        'full_name' => $activity->code . ' - ' . $activity->name
                                    ];
                                })
                            ];
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $actionsData,
                'component' => [
                    'id' => $component->rp_components_id,
                    'name' => $component->name,
                    'code' => $component->code
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading RP actions with activities', [
                'component_id' => $componentId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading data'
            ], 500);
        }
    }

    /**
     * Get all RP Components with basic info
     * From first code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRPComponents(Request $request)
    {
        try {
            $components = RpComponent::whereNull('deleted_at')
                ->orderBy('code')
                ->limit(10) // Limit for performance
                ->get(['rp_components_id', 'code', 'name', 'description']);
            
            return response()->json([
                'success' => true,
                'data' => $components
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading RP components', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading components'
            ], 500);
        }
    }

    /**
     * Get RP Component details with hierarchy counts
     * From first code
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRPComponentDetails(Request $request, $id)
    {
        try {
            $component = RpComponent::withCount([
                'programs',
                'programs as units_count' => function($query) {
                    $query->select(DB::raw('count(distinct rp_units.rp_units_id)'))
                          ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                          ->whereNull('rp_units.deleted_at');
                },
                'programs as actions_count' => function($query) {
                    $query->select(DB::raw('count(distinct rp_actions.rp_actions_id)'))
                          ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                          ->join('rp_actions', 'rp_units.rp_units_id', '=', 'rp_actions.rp_units_id')
                          ->whereNull('rp_actions.deleted_at');
                },
                'programs as activities_count' => function($query) {
                    $query->select(DB::raw('count(distinct rp_activities.rp_activities_id)'))
                          ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                          ->join('rp_actions', 'rp_units.rp_units_id', '=', 'rp_actions.rp_units_id')
                          ->join('rp_activities', 'rp_actions.rp_actions_id', '=', 'rp_activities.rp_actions_id')
                          ->whereNull('rp_activities.deleted_at');
                }
            ])
            ->where('rp_components_id', $id)
            ->whereNull('deleted_at')
            ->first();
            
            if (!$component) {
                return response()->json([
                    'success' => false,
                    'message' => 'Component not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $component
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading RP component details', [
                'component_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading component details'
            ], 500);
        }
    }

    /**
     * Test data methods (from first code)
     */
    private function getTestActivities()
    {
        return [
            [
                'rp_activities_id' => 'activity-1',
                'name' => 'Training Session 1',
                'code' => 'ACT-001',
                'rp_action_id' => 'action-1',
                'action_name' => 'Capacity Building Action'
            ],
            [
                'rp_activities_id' => 'activity-2',
                'name' => 'Workshop 1',
                'code' => 'ACT-002',
                'rp_action_id' => 'action-1',
                'action_name' => 'Capacity Building Action'
            ],
            [
                'rp_activities_id' => 'activity-3',
                'name' => 'Mentoring Program',
                'code' => 'ACT-003',
                'rp_action_id' => 'action-2',
                'action_name' => 'Professional Development'
            ]
        ];
    }
    
    private function getTestActionsWithActivities()
    {
        return [
            [
                'action_id' => 'action-1',
                'action_name' => 'Capacity Building Action',
                'action_code' => 'ACT-CAP-001',
                'activities' => [
                    [
                        'rp_activities_id' => 'activity-1',
                        'name' => 'Training Session 1',
                        'code' => 'ACT-001',
                        'rp_action_id' => 'action-1',
                        'full_name' => 'ACT-001 - Training Session 1'
                    ],
                    [
                        'rp_activities_id' => 'activity-2',
                        'name' => 'Workshop 1',
                        'code' => 'ACT-002',
                        'rp_action_id' => 'action-1',
                        'full_name' => 'ACT-002 - Workshop 1'
                    ]
                ]
            ],
            [
                'action_id' => 'action-2',
                'action_name' => 'Professional Development',
                'action_code' => 'ACT-PRO-001',
                'activities' => [
                    [
                        'rp_activities_id' => 'activity-3',
                        'name' => 'Mentoring Program',
                        'code' => 'ACT-003',
                        'rp_action_id' => 'action-2',
                        'full_name' => 'ACT-003 - Mentoring Program'
                    ]
                ]
            ]
        ];
    }
}