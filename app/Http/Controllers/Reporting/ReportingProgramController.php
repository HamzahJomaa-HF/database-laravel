<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpProgram;
use App\Models\RpComponent;

class ReportingProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = RpProgram::with('component')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('reporting.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $components = RpComponent::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_components_id', 'name', 'code']);
        
        return view('reporting.programs.create', compact('components'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'component_id' => 'required|uuid|exists:rp_components,rp_components_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_programs,code',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $program = RpProgram::create($validated);

        return redirect()->route('reporting.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpProgram $rpProgram)
    {
        $rpProgram->load([
            'component',
            'units',
            'units.actions',
            'units.actions.activities'
        ]);
        
        // Calculate totals
        $totalUnits = $rpProgram->units->count();
        $totalActions = $rpProgram->units->flatMap->actions->count();
        $totalBudget = $rpProgram->units->sum('budget');
        
        return view('reporting.programs.show', compact('rpProgram', 'totalUnits', 'totalActions', 'totalBudget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpProgram $rpProgram)
    {
        $components = RpComponent::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_components_id', 'name', 'code']);
        
        return view('reporting.programs.edit', compact('rpProgram', 'components'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpProgram $rpProgram)
    {
        $validated = $request->validate([
            'component_id' => 'required|uuid|exists:rp_components,rp_components_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_programs,code,' . $rpProgram->rp_programs_id . ',rp_programs_id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $rpProgram->update($validated);

        return redirect()->route('reporting.programs.show', $rpProgram)
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpProgram $rpProgram)
    {
        $rpProgram->delete();

        return redirect()->route('reporting.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Get programs by component ID (for AJAX requests)
     */
    public function getByComponent($componentId)
    {
        $programs = RpProgram::where('component_id', $componentId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['rp_programs_id', 'name', 'code']);
        
        return response()->json($programs);
    }

    /**
     * Get program statistics
     */
    public function getStatistics($id)
    {
        $program = RpProgram::with([
            'units.actions.activities',
            'units.actions.targetActions'
        ])->findOrFail($id);
        
        $statistics = [
            'total_units' => $program->units->count(),
            'total_actions' => $program->units->flatMap->actions->count(),
            'total_activities' => $program->units->flatMap->actions->flatMap->activities->count(),
            'total_budget' => $program->units->sum('budget'),
            'status_summary' => [
                'active' => $program->units->where('is_active', true)->count(),
                'inactive' => $program->units->where('is_active', false)->count(),
            ]
        ];
        
        return response()->json($statistics);
    }

    /**
     * Duplicate a program
     */
    public function duplicate($id)
    {
        $originalProgram = RpProgram::findOrFail($id);
        
        // Create a duplicate
        $newProgram = $originalProgram->replicate();
        $newProgram->code = $originalProgram->code . '_COPY_' . time();
        $newProgram->name = $originalProgram->name . ' (Copy)';
        $newProgram->save();
        
        // Optionally duplicate units and hierarchy (you can add this later)
        
        return redirect()->route('reporting.programs.edit', $newProgram)
            ->with('success', 'Program duplicated successfully.');
    }
}