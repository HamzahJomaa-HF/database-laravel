<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpTargetAction;
use App\Models\RpAction;

class ReportingTargetActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpTargetAction::with('action.unit.program.component');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('action_id')) {
            $query->where('action_id', $request->action_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('target_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $targets = $query->orderBy('target_date', 'asc')->paginate(20);
        
        return view('reporting.target-actions.index', compact('targets'));
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
        
        $statuses = ['pending', 'in_progress', 'achieved', 'partially_achieved', 'not_achieved', 'cancelled'];
        
        return view('reporting.target-actions.create', compact('actions', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'action_id' => 'required|uuid|exists:rp_actions,rp_actions_id',
            'external_id' => 'nullable|uuid',
            'target_name' => 'required|string',
            'description' => 'nullable|string',
            'target_value' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string',
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date'
        ]);

        $targetAction = RpTargetAction::create($validated);

        return redirect()->route('reporting.target-actions.index')
            ->with('success', 'Target created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpTargetAction $rpTargetAction)
    {
        $rpTargetAction->load('action.unit.program.component');
        
        return view('reporting.target-actions.show', compact('rpTargetAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpTargetAction $rpTargetAction)
    {
        $actions = RpAction::where('is_active', true)
            ->with('unit.program.component')
            ->orderBy('name')
            ->get();
        
        $statuses = ['pending', 'in_progress', 'achieved', 'partially_achieved', 'not_achieved', 'cancelled'];
        
        return view('reporting.target-actions.edit', compact('rpTargetAction', 'actions', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpTargetAction $rpTargetAction)
    {
        $validated = $request->validate([
            'action_id' => 'required|uuid|exists:rp_actions,rp_actions_id',
            'external_id' => 'nullable|uuid',
            'target_name' => 'required|string',
            'description' => 'nullable|string',
            'target_value' => 'nullable|numeric',
            'unit_of_measure' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string',
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date'
        ]);

        $rpTargetAction->update($validated);

        return redirect()->route('reporting.target-actions.show', $rpTargetAction)
            ->with('success', 'Target updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpTargetAction $rpTargetAction)
    {
        $rpTargetAction->delete();

        return redirect()->route('reporting.target-actions.index')
            ->with('success', 'Target deleted successfully.');
    }

    /**
     * Get targets by action ID
     */
    public function getByAction($actionId)
    {
        $targets = RpTargetAction::where('action_id', $actionId)
            ->orderBy('target_date', 'asc')
            ->get();
        
        return response()->json($targets);
    }

    /**
     * Update achievement status
     */
    public function updateAchievement(Request $request, $id)
    {
        $target = RpTargetAction::findOrFail($id);
        
        $validated = $request->validate([
            'achieved_value' => 'nullable|numeric',
            'achieved_date' => 'nullable|date',
            'status' => 'required|in:achieved,partially_achieved,not_achieved'
        ]);
        
        $target->update($validated);
        
        return redirect()->back()
            ->with('success', 'Achievement updated successfully.');
    }

    /**
     * Calculate achievement percentage
     */
    public function calculateAchievement($id)
    {
        $target = RpTargetAction::findOrFail($id);
        
        if (!$target->target_value || $target->target_value <= 0) {
            return response()->json(['achievement_percentage' => 0]);
        }
        
        $percentage = ($target->achieved_value / $target->target_value) * 100;
        
        return response()->json([
            'achievement_percentage' => $percentage,
            'target_name' => $target->target_name,
            'target_value' => $target->target_value,
            'achieved_value' => $target->achieved_value,
            'unit_of_measure' => $target->unit_of_measure
        ]);
    }

    /**
     * Get targets with overdue status
     */
    public function getOverdueTargets()
    {
        $overdueTargets = RpTargetAction::where('status', '!=', 'achieved')
            ->where('status', '!=', 'cancelled')
            ->whereDate('target_date', '<', now())
            ->with('action.unit.program.component')
            ->orderBy('target_date', 'asc')
            ->get();
        
        return response()->json($overdueTargets);
    }

    /**
     * Get achievement summary by action
     */
    public function getAchievementSummary($actionId)
    {
        $targets = RpTargetAction::where('action_id', $actionId)->get();
        
        $summary = [
            'total_targets' => $targets->count(),
            'achieved' => $targets->where('status', 'achieved')->count(),
            'partially_achieved' => $targets->where('status', 'partially_achieved')->count(),
            'not_achieved' => $targets->where('status', 'not_achieved')->count(),
            'pending' => $targets->where('status', 'pending')->count(),
            'in_progress' => $targets->where('status', 'in_progress')->count(),
            'total_target_value' => $targets->sum('target_value'),
            'total_achieved_value' => $targets->sum('achieved_value'),
            'overall_achievement' => $targets->sum('target_value') > 0 
                ? ($targets->sum('achieved_value') / $targets->sum('target_value')) * 100 
                : 0
        ];
        
        return response()->json($summary);
    }
}