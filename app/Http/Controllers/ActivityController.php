<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\RpComponent;
use App\Models\RpActivity;
use App\Models\RpAction;
use App\Models\ActionPlan; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Support\Str ;

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
            'title',
            'activity_type',
            'venue',
            'status',
            'start_date_from',
            'end_date_to'
        ]);

        // Apply filters
        if ($request->filled('title')) {
            $query->where(function ($q) use ($request) {
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
            'activity_title_en' => 'required_without:activity_title_ar|string|max:255',
            'activity_title_ar' => 'required_without:activity_title_en|string|max:255',
            'activity_type' => 'required|string|max:100',
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

        // Handle RP Activities mapping (NEW CODE)
        if (!empty($rpActivities)) {
            foreach ($rpActivities as $rpActivityId) {
                DB::table('rp_activity_mappings')->insert([
                    'rp_activity_mappings_id' => (string) \Illuminate\Support\Str::uuid(),
                    'rp_activities_id' => $rpActivityId,
                    'activity_id' => $activity->activity_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
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
    /**
     * Show the form for editing the specified activity.
     */
 
    public function edit($id)
    {
        $activity = Activity::findOrFail($id);
        
        // DEBUG: Check what's actually in the database
        Log::info('DEBUG ACTIVITY DATA:', [
            'activity_id' => $activity->activity_id,
            'rp_component_id' => $activity->rp_component_id,
            'action_plan_id' => $activity->action_plan_id, // Add this
            'rp_activities_raw' => $activity->rp_activities,
            'rp_activities_decoded' => json_decode($activity->rp_activities, true),
        ]);

        $projects = ProjectActivity::where('project_activities.activity_id', $id)
            ->leftJoin('projects', 'projects.project_id', '=', 'project_activities.project_id')
            ->leftJoin('programs as p', 'p.program_id', '=', 'projects.program_id')
            ->leftJoin('programs as pp', 'pp.program_id', '=', 'p.parent_program_id')
            ->select([
                'project_activities.*',
                'projects.*',
                'p.program_id as program_id',
                'p.name as program_name',
                'pp.program_id as parent_program_id',
                'pp.name as parent_program_name',
            ])->get();

        // ============================================
        // PROGRAMS (existing code - good format)
        // ============================================
        $programs = Program::whereIn('program_type', ['Center', 'Local Program/Network', 'Flagship'])
            ->orderBy('name')
            ->get(['program_id', 'name', 'external_id']);

        // GOOD FORMAT: Using pluck() -> unique() -> toArray()
        $selected_program = $projects->pluck('parent_program_id')->unique()->toArray();
        $selected_project_ids = $projects->pluck('project_id')->unique()->toArray();

        // ============================================
        // ACTION PLANS - New section
        // ============================================
        $actionPlans = ActionPlan::orderBy('title')->get(['action_plan_id', 'title', 'start_date', 'end_date']);
        
        // Get selected action plan from activity (assuming you have action_plan_id column)
        $selectedActionPlanId = $activity->action_plan_id;
        $selectedActionPlanIdSingle = $activity->action_plan_id;

        // ============================================
        // RP COMPONENTS - Filtered by selected action plan
        // ============================================
        $rpComponents = [];
        
        if ($selectedActionPlanId) {
            $rpComponents = RpComponent::where('action_plan_id', $selectedActionPlanId)
                ->whereNull('deleted_at')
                ->orderBy('code')
                ->get(['rp_components_id', 'code', 'name', 'action_plan_id']);
        } else {
            // If no action plan selected, show all components (limited)
            $rpComponents = RpComponent::whereNull('deleted_at')
                ->orderBy('code')
                ->limit(50)
                ->get(['rp_components_id', 'code', 'name', 'action_plan_id']);
        }

        // For component (single selection)
        $selectedComponentIdSingle = $activity->rp_component_id;

        // ============================================
        // RP ACTIVITIES
        // ============================================
        $selectedRpActivityIds = [];
        
          
    // ✅ NEW WAY (from rp_activity_mappings table) - CORRECT!
    $selectedRpActivityIds = DB::table('rp_activity_mappings')
        ->where('activity_id', $activity->activity_id)
        ->pluck('rp_activities_id')
        ->map(function($id) {
            return (string) $id;
        })
        ->toArray();

        // ============================================
        // Return view
        // ============================================
        return view('activities.edit', compact(
            'activity',
            'actionPlans',           // New: Action plans
            'selectedActionPlanIdSingle', // New: Selected action plan
            'rpComponents',
            'selectedComponentIdSingle',
            'selectedRpActivityIds',
            'programs',
            'selected_program',
            'selected_project_ids',
            'projects'
        ));
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        // Validate the request - ADD action_plan_id
        $validated = $request->validate([
            'activity_title_en' => 'required|string|max:255',
            'activity_title_ar' => 'nullable|string|max:255',
            'activity_type' => 'required|string|max:100',
            'projects' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'content_network' => 'nullable|string',
            'action_plan_id' => 'nullable|exists:action_plans,action_plan_id', // NEW
            'rp_component_id' => 'nullable|exists:rp_components,rp_components_id',
            'rp_activities' => 'nullable|array',
            'focal_points' => 'nullable|array',
            'operational_support' => 'nullable|array',
        ]);

        // Extract the specific arrays from the request
        $extractedData = [
            'rp_activities' => $request->input('rp_activities', []),
            'focal_points' => $request->input('focal_points', []),
            'projects' => $request->input('projects', []),
        ];

        // Or extract them to separate variables
        $rpActivities = $request->input('rp_activities', []);
        $focalPoints = $request->input('focal_points', []);
        $projects = $request->input('projects', []);

        // Remove from validated array if they were included
        unset($validated['rp_activities'], $validated['focal_points'], $validated['projects']);

        // Handle operational_support
        if (isset($validated['operational_support'])) {
            $validated['operational_support'] = json_encode($validated['operational_support']);
        } else {
            $validated['operational_support'] = null;
        }

        // Handle focal_points as JSON
        if (!empty($focalPoints)) {
            $validated['focal_points'] = json_encode($focalPoints);
        } else {
            $validated['focal_points'] = null;
        }

       

        // Update the activity with ALL validated data including action_plan_id, component and activities
        $activity->update($validated);

        // Get project ids from request (ensure it's an array of ids)
        $projects = $projects ?? [];
        $projects = array_values(array_filter($projects)); // remove null/empty
        $projects = array_unique($projects);               // avoid duplicates

        // Existing project_ids for this activity
        $existing = ProjectActivity::where('activity_id', $id)
            ->pluck('project_id')
            ->toArray();

        // Add new ones
        $toInsert = array_diff($projects, $existing);
        foreach ($toInsert as $projectId) {
            ProjectActivity::create([
                'activity_id' => $id,
                'project_id'  => $projectId,
            ]);
        }

        // Delete removed ones
        $toDelete = array_diff($existing, $projects);
        if (!empty($toDelete)) {
            ProjectActivity::where('activity_id', $id)
                ->whereIn('project_id', $toDelete)
                ->delete();
        }

        // Handle RP Activities mapping in separate table (NEW CODE - similar to projects)
        if (!empty($rpActivities)) {
            // First, delete existing mappings for this activity
            DB::table('rp_activity_mappings')
                ->where('activity_id', $activity->activity_id)
                ->delete();

            // Then create new mappings
            foreach ($rpActivities as $rpActivityId) {
                DB::table('rp_activity_mappings')->insert([
                    'rp_activity_mappings_id' => (string) \Illuminate\Support\Str::uuid(),
                    'rp_activities_id' => $rpActivityId,
                    'activity_id' => $activity->activity_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // If no RP activities selected, delete any existing mappings
            DB::table('rp_activity_mappings')
                ->where('activity_id', $activity->activity_id)
                ->delete();
        }

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified activity from storage using soft delete.
     */
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        
        // Use soft delete instead of permanent delete
        $activity->delete();
        
        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    // Add these new AJAX methods to your controller:

    /**
     * Get RP Components by Action Plan ID (AJAX endpoint)
     */
    public function getComponentsByActionPlan(Request $request)
    {
        try {
            $request->validate([
                'action_plan_id' => 'required|uuid'
            ]);

            $actionPlanId = $request->action_plan_id;

            $components = RpComponent::where('action_plan_id', $actionPlanId)
                ->whereNull('deleted_at')
                ->orderBy('code')
                ->get(['rp_components_id', 'code', 'name']);

            return response()->json([
                'success' => true,
                'action_plan_id' => $actionPlanId,
                'components' => $components,
                'count' => $components->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getComponentsByActionPlan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all action plans (for dropdown)
     */
    public function getActionPlans(Request $request)
    {
        try {
            $actionPlans = ActionPlan::orderBy('title')
                ->get(['action_plan_id', 'title', 'start_date', 'end_date']);

            return response()->json([
                'success' => true,
                'action_plans' => $actionPlans
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading action plans', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading action plans'
            ], 500);
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
                ->map(function ($activity) {
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
     * Get Projects by Program ID (AJAX endpoint)
     */
    public function getProjectsByProgram(Request $request)
    {
        try {

            Log::info('getProjectsByProgram called', $request->all());

            // Validate request
            $request->validate([
                'program_id' => 'required|uuid',
            ]);

            $programId = $request->program_id;

            $projects = Project::with('program')
                ->whereHas('program', function ($query) use ($programId) {
                    $query->where('parent_program_id', $programId);
                })
                ->orderBy('name')
                ->get();


            return response()->json([
                'success' => true,
                'program_id' => $programId,
                'projects' => $projects,
                'count' => $projects->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getProjectsByProgram', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => $request->all()
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

        Log::info('getRPActionsWithActivities called', [
            'component_id' => $componentId,
            'request_all' => $request->all()
        ]);

        try {
            // Debug: Check if component exists
            $componentExists = RpComponent::where('rp_components_id', $componentId)
                ->whereNull('deleted_at')
                ->exists();
                
            Log::info('Component exists check:', [
                'component_id' => $componentId,
                'exists' => $componentExists
            ]);

            // Get the component with its full hierarchy
            $component = RpComponent::with([
                'programs.units.actions.activities' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->orderBy('code')
                        ->select(['rp_activities_id', 'rp_actions_id', 'name', 'code']);
                }
            ])
                ->where('rp_components_id', $componentId)
                ->whereNull('deleted_at')
                ->first();

            if (!$component) {
                Log::warning('Component not found', ['component_id' => $componentId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Component not found',
                    'debug' => ['component_id' => $componentId]
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
                                'activities' => $action->activities->map(function ($activity) {
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
                'programs as units_count' => function ($query) {
                    $query->select(DB::raw('count(distinct rp_units.rp_units_id)'))
                        ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                        ->whereNull('rp_units.deleted_at');
                },
                'programs as actions_count' => function ($query) {
                    $query->select(DB::raw('count(distinct rp_actions.rp_actions_id)'))
                        ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                        ->join('rp_actions', 'rp_units.rp_units_id', '=', 'rp_actions.rp_units_id')
                        ->whereNull('rp_actions.deleted_at');
                },
                'programs as activities_count' => function ($query) {
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