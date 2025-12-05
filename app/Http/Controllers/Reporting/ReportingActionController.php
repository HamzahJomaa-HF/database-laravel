<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpAction;
use App\Models\RpUnit;

class ReportingActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actions = RpAction::with('unit.program.component')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('reporting.actions.index', compact('actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = RpUnit::where('is_active', true)
            ->with('program.component')
            ->orderBy('name')
            ->get();
        
        return view('reporting.actions.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|uuid|exists:rp_units,rp_units_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_actions,code',
            'description' => 'nullable|string',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'allocated_budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $action = RpAction::create($validated);

        return redirect()->route('reporting.actions.index')
            ->with('success', 'Action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpAction $rpAction)
    {
        $rpAction->load([
            'unit.program.component',
            'activities',
            'targetActions'
        ]);
        
        return view('reporting.actions.show', compact('rpAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpAction $rpAction)
    {
        $units = RpUnit::where('is_active', true)
            ->with('program.component')
            ->orderBy('name')
            ->get();
        
        return view('reporting.actions.edit', compact('rpAction', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpAction $rpAction)
    {
        $validated = $request->validate([
            'unit_id' => 'required|uuid|exists:rp_units,rp_units_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_actions,code,' . $rpAction->rp_actions_id . ',rp_actions_id',
            'description' => 'nullable|string',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'allocated_budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $rpAction->update($validated);

        return redirect()->route('reporting.actions.show', $rpAction)
            ->with('success', 'Action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpAction $rpAction)
    {
        $rpAction->delete();

        return redirect()->route('reporting.actions.index')
            ->with('success', 'Action deleted successfully.');
    }

    /**
     * Get actions by unit ID (for AJAX requests)
     */
    public function getByUnit($unitId)
    {
        $actions = RpAction::where('unit_id', $unitId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['rp_actions_id', 'name', 'code']);
        
        return response()->json($actions);
    }

    /**
     * Get action details with hierarchy
     */
    public function getDetails($id)
    {
        $action = RpAction::with([
            'unit.program.component',
            'activities',
            'targetActions'
        ])->findOrFail($id);
        
        return response()->json($action);
    }
}