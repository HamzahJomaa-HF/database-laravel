<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\RpComponent;
use App\Models\RpActivity;

class ActivityController extends Controller
{
    /**
     * Show the form for editing an activity.
     * This version doesn't require an ID parameter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        // Get RP Components from database (limit to 5 as requested)
        $rpComponents = RpComponent::whereNull('deleted_at')
            ->orderBy('code')
            ->limit(5)
            ->get();
        
        // Get an activity for testing
        $activity = Activity::first();
        
        // If no activity exists, create a dummy one for testing
        if (!$activity) {
            $activity = (object) [
                'activity_id' => 'test-id',
                'activity_title_en' => 'Leadership Training Workshop',
                'activity_title_ar' => 'ورشة تدريب القيادة',
                'activity_type' => 'Capacity Building',
                'content_network' => 'Training program for school leaders focusing on management skills.',
                'start_date' => '2024-01-15',
                'end_date' => '2024-01-17',
                'venue' => 'Hariri Foundation Headquarters',
                'operational_support' => json_encode(['Logistics']),
            ];
        } else {
            // Load reporting activities relationship if it exists
            if (method_exists($activity, 'reportingActivities')) {
                $activity->load('reportingActivities');
            }
        }
        
        // Get selected RP component and activities for this activity
        $selectedComponent = null;
        $selectedActivities = collect();
        
        // Try to find the component from reporting activities
        if (isset($activity->reportingActivities) && $activity->reportingActivities->isNotEmpty()) {
            $selectedActivities = $activity->reportingActivities;
            
            // Get the first activity's component through relationships
            if ($firstRpActivity = $selectedActivities->first()) {
                // Load relationships to get to component
                $firstRpActivity->load([
                    'action.unit.program.component' => function($query) {
                        $query->whereNull('deleted_at');
                    }
                ]);
                
                if ($firstRpActivity->action && 
                    $firstRpActivity->action->unit && 
                    $firstRpActivity->action->unit->program && 
                    $firstRpActivity->action->unit->program->component) {
                    $selectedComponent = $firstRpActivity->action->unit->program->component;
                }
            }
        }
        
        return view('activities.edit', compact(
            'activity',
            'rpComponents',
            'selectedComponent',
            'selectedActivities'
        ));
    }
    
    /**
     * Get RP Activities by Component ID (AJAX endpoint)
     * 
     * Now using Eloquent relationships instead of raw SQL joins
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
        
        \Log::info('getRPActivities called', ['component_id' => $componentId]);
        
        // For testing - check if it's a test ID
        if (strpos($componentId, 'test-') === 0) {
            \Log::info('Using test data for component', ['component_id' => $componentId]);
            
            return response()->json([
                'success' => true,
                'data' => $this->getTestActivities()
            ]);
        }
        
        try {
            \Log::info('Querying database for component using Eloquent', ['component_id' => $componentId]);
            
            // Using Eloquent relationships instead of raw joins
            $activities = RpActivity::with('action') // Eager load the action relationship
                ->whereHas('action.unit.program.component', function($query) use ($componentId) {
                    $query->where('rp_components_id', $componentId)
                          ->whereNull('deleted_at');
                })
                ->whereNull('rp_activities.deleted_at')
                ->orderBy('code')
                ->limit(5)
                ->get()
                ->map(function($activity) {
                    return [
                        'rp_activities_id' => $activity->rp_activities_id,
                        'name' => $activity->name,
                        'code' => $activity->code,
                        'rp_action_id' => $activity->rp_actions_id,
                        'action_name' => $activity->action->name ?? null
                    ];
                });
            
            \Log::info('Found activities', [
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
            \Log::error('Error loading RP activities', [
                'component_id' => $componentId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading activities'
            ], 500);
        }
    }
    
    /**
     * Get RP Actions grouped by Component ID
     * Returns actions with their activities as sub-items
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
        
        \Log::info('getRPActionsWithActivities called', ['component_id' => $componentId]);
        
        // For testing
        if (strpos($componentId, 'test-') === 0) {
            return response()->json([
                'success' => true,
                'data' => $this->getTestActionsWithActivities()
            ]);
        }
        
        try {
            // Get the component with its full hierarchy
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
                                        'rp_action_id' => $activity->rp_actions_id
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
            \Log::error('Error loading RP actions with activities', [
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
            \Log::error('Error loading RP components', [
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
                    $query->select(\DB::raw('count(distinct rp_units.rp_units_id)'))
                          ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                          ->whereNull('rp_units.deleted_at');
                },
                'programs as actions_count' => function($query) {
                    $query->select(\DB::raw('count(distinct rp_actions.rp_actions_id)'))
                          ->join('rp_units', 'rp_programs.rp_programs_id', '=', 'rp_units.rp_programs_id')
                          ->join('rp_actions', 'rp_units.rp_units_id', '=', 'rp_actions.rp_units_id')
                          ->whereNull('rp_actions.deleted_at');
                },
                'programs as activities_count' => function($query) {
                    $query->select(\DB::raw('count(distinct rp_activities.rp_activities_id)'))
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
            \Log::error('Error loading RP component details', [
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
     * Test data methods (keep these private)
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
                        'rp_action_id' => 'action-1'
                    ],
                    [
                        'rp_activities_id' => 'activity-2',
                        'name' => 'Workshop 1',
                        'code' => 'ACT-002',
                        'rp_action_id' => 'action-1'
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
                        'rp_action_id' => 'action-2'
                    ]
                ]
            ]
        ];
    }
}