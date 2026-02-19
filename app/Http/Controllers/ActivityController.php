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
use App\Models\RpActivityMapping;
use App\Models\Portfolio;
use App\Models\PortfolioActivity;

use Illuminate\Support\Str;

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
        // ============================================
        // ACTION PLANS - Same as edit
        // ============================================
        $actionPlans = ActionPlan::orderBy('title')->get(['action_plan_id', 'title', 'start_date', 'end_date']);
        
        $portfolios = Portfolio::orderBy('created_at', 'desc')->get(['portfolio_id', 'name', 'description', 'type', 'external_id']);

        // For create, no selected action plan
        $selectedActionPlanIdSingle = null;

        // ============================================
        // RP COMPONENTS - Initially show all (limited)
        // ============================================
        $rpComponents = RpComponent::whereNull('deleted_at')
            ->orderBy('code')
            ->get(['rp_components_id', 'code', 'name', 'action_plan_id']);

        // For create, no selected component
        $selectedComponentIdSingle = null;
        $selectedRpActivityIds = [];

        // ============================================
        // PROGRAMS - Same as edit
        // ============================================
        $programs = Program::whereIn('program_type', ['Center', 'Local Program', 'Flagship'])
            ->orderBy('name')
            ->get(['program_id', 'name', 'external_id']);

        // For create, no selected programs/projects
        $selected_program = [];
        $selected_project_ids = [];
        $projects = collect(); // Empty collection

        // ============================================
        // Return view with same variables as edit
        // ============================================
        return view('activities.create', compact(
            'actionPlans',
            'selectedActionPlanIdSingle',
            'rpComponents',
            'selectedComponentIdSingle',
            'selectedRpActivityIds',
            'programs',
            'selected_program',
            'selected_project_ids',
            'projects',
            'portfolios'
        ));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(Request $request)
{
   
    try {
        DB::beginTransaction();

        // Validate the request
        $validated = $request->validate([
            'activity_title_en' => 'required_without:activity_title_ar|string|max:255',
            'activity_title_ar' => 'nullable|string|max:255|required_without:activity_title_en',
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
        'portfolios.*' => 'exists:portfolios,portfolio_id', 
                    'maximum_capacity' => 'nullable|integer|min:0',

        ]);

        // Extract arrays from request BEFORE removing them
        $rpActivities = $request->input('rp_activities', []);
        $projects = $request->input('projects', []);
        $focalPoints = $request->input('focal_points', []);
        $operationalSupport = $request->input('operational_support', []);
        $portfolios = $request->input('portfolios', []);

        // Convert arrays to JSON (only for columns that exist in activities table)
        if (isset($validated['operational_support'])) {
            $validated['operational_support'] = json_encode($validated['operational_support']);
        }

        // Remove arrays that don't exist in activities table
        unset($validated['rp_activities'], $validated['projects'], $validated['focal_points'], $validated['portfolios']);

        // Generate a unique activity_id if not provided
        if (empty($validated['activity_id'])) {
            $validated['activity_id'] = (string) Str::uuid();
        }

        // Create the activity and get the created instance
        $activity = Activity::create($validated);

        // ============================================
        // Handle Projects (project_activities table)
        // ============================================
        if (!empty($projects)) {
            $projects = array_unique(array_filter($projects)); // remove null/empty and duplicates
            
            foreach ($projects as $projectId) {
                ProjectActivity::create([
                    'project_activity_id' => (string) Str::uuid(),
                    'activity_id' => $activity->activity_id,
                    'project_id' => $projectId,
                ]);
            }
        }


        if (!empty($portfolios)) {
                // (Optional) remove old links for this activity (if editing/refreshing)
            PortfolioActivity::where('activity_id', $activity->activity_id)->delete();
            $portfolios = array_unique(array_filter($portfolios)); // remove null/empty and duplicates
            
            // Insert new rows
            foreach ($portfolios as $portfolioId) {
                PortfolioActivity::create([
                    'activity_id' => $activity->activity_id,
                    'portfolio_id' => $portfolioId,
                ]);
            }
        }

        // ============================================
        // Handle RP Activities mapping (rp_activity_mappings table)
        // ============================================
        if (!empty($rpActivities)) {
            $rpActivities = array_unique(array_filter($rpActivities)); // remove null/empty and duplicates
            
            foreach ($rpActivities as $rpActivityId) {
                DB::table('rp_activity_mappings')->insert([
                    'rp_activity_mappings_id' => (string) Str::uuid(),
                    'rp_activities_id' => $rpActivityId,
                    'activity_id' => $activity->activity_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ============================================
        // Handle Focal Points (if you store in activities table as JSON)
        // ============================================
        if (!empty($focalPoints)) {
            // Update the activity with focal_points JSON
            $activity->update([
                'focal_points' => json_encode($focalPoints)
            ]);
        }

        DB::commit();

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating activity: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return back()
            ->withInput()
            ->with('error', 'Error creating activity: ' . $e->getMessage());
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

        $selected_portfolios = PortfolioActivity::where('activity_id', $id)
            ->get()->pluck('portfolio_id')->toArray();


        $rp_activities = RpActivityMapping::where('rp_activity_mappings.activity_id', $id)
            ->leftJoin('rp_activities', 'rp_activities.rp_activities_id', '=', 'rp_activity_mappings.rp_activities_id')
            ->leftJoin('rp_actions', 'rp_actions.rp_actions_id', '=', 'rp_activities.rp_actions_id')
            ->leftJoin('rp_units', 'rp_units.rp_units_id', '=', 'rp_actions.rp_units_id')
            ->leftJoin('rp_programs', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
            ->leftJoin('rp_components', 'rp_components.rp_components_id', '=', 'rp_programs.rp_components_id')
            ->leftJoin('action_plans', 'action_plans.action_plan_id', '=', 'rp_components.action_plan_id')
            ->select([
                'rp_activities.name as activity_name',
                'rp_actions.name as action_name',
                'rp_units.name as unit_name',
                'rp_programs.name as program_name',
                'rp_components.name as component_name',
                'action_plans.title as action_plan_name',
                'rp_activities.rp_activities_id',
                'rp_components.rp_components_id',
                'action_plans.action_plan_id',
            ])->get();
        
        
        // ============================================
        // PROGRAMS (existing code - good format)
        // ============================================
        $programs = Program::whereIn('program_type', ['Center', 'Local Program', 'Flagship'])
            ->orderBy('name')
            ->get(['program_id', 'name', 'external_id']);

        // GOOD FORMAT: Using pluck() -> unique() -> toArray()
        $selected_program = $projects->pluck('parent_program_id')->unique()->toArray();
        $selected_project_ids = $projects->pluck('project_id')->unique()->toArray();
                $portfolios = Portfolio::orderBy('created_at', 'desc')->get(['portfolio_id', 'name', 'description', 'type', 'external_id']);

        $actionPlans = ActionPlan::orderBy('title')->get(['action_plan_id', 'title', 'start_date', 'end_date']);


        $selected_action_plan_id = $rp_activities->pluck('action_plan_id')->first();
        $selected_component_id = $rp_activities->pluck('rp_components_id')->first();
        $selected_rp_activity_ids = $rp_activities->pluck('rp_activities_id')->unique()->toArray();
        
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
            'programs',
            'selected_program',
            'selected_project_ids',
            'projects',
            'selected_action_plan_id',
            'selected_component_id',
            'selected_rp_activity_ids',
            'portfolios',
            'selected_portfolios'
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
            'maximum_capacity' => 'nullable|integer|min:0',
                    'portfolios.*' => 'exists:portfolios,portfolio_id', 
                    'maximum_capacity' => 'nullable|integer|min:0',
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
        $portfolios = $request->input('portfolios', []);

        // Remove from validated array if they were included
        unset($validated['rp_activities'], $validated['focal_points'], $validated['projects'], $validated['portfolios']);

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

        // Handle rp_activities as JSON in the activity table
        if (!empty($rpActivities)) {
            $validated['rp_activities'] = json_encode($rpActivities);
        } else {
            $validated['rp_activities'] = null;
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
        

        if (!empty($portfolios)) {

            // Remove existing relations
            PortfolioActivity::where('activity_id', $id)->forceDelete();

            // Clean array (remove nulls and duplicates)
            $portfolios = array_unique(array_filter($portfolios));

            // Insert new rows
            foreach ($portfolios as $portfolioId) {
                PortfolioActivity::create([
                    'activity_id' => $id,
                    'portfolio_id' => $portfolioId,
                ]);
            }
        }else{
            // If no portfolios selected, remove existing relations
            PortfolioActivity::where('activity_id', $id)->forceDelete();
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


        try {

            // Simple query for testing
            $activities = RpActivity::whereNull('deleted_at')
                ->orderBy('code')
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

        

        try {
            // Debug: Check if component exists
            $componentExists = RpComponent::where('rp_components_id', $componentId)
                ->whereNull('deleted_at')
                ->exists();
                
            

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
            $actionPlanId = $request->query('action_plan_id');
            $components = RpComponent::where("action_plan_id", $actionPlanId)
                ->orderBy('code')
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