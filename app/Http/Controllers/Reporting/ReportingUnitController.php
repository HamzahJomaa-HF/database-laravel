<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpUnit;
use App\Models\RpProgram;

class ReportingUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = RpUnit::with('program.component')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('reporting.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = RpProgram::where('is_active', true)
            ->with('component')
            ->orderBy('name')
            ->get(['rp_programs_id', 'name', 'code']);
        
        $unitTypes = [
            'department' => 'Department',
            'division' => 'Division',
            'section' => 'Section',
            'team' => 'Team',
            'working_group' => 'Working Group',
            'committee' => 'Committee',
            'task_force' => 'Task Force'
        ];
        
        return view('reporting.units.create', compact('programs', 'unitTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|uuid|exists:rp_programs,rp_programs_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_units,code',
            'unit_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $unit = RpUnit::create($validated);

        return redirect()->route('reporting.units.index')
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpUnit $rpUnit)
    {
        $rpUnit->load([
            'program.component',
            'actions',
            'actions.activities',
            'actions.targetActions'
        ]);
        
        // Calculate statistics
        $totalActions = $rpUnit->actions->count();
        $totalActiveActions = $rpUnit->actions->where('is_active', true)->count();
        $totalActivities = $rpUnit->actions->flatMap->activities->count();
        
        return view('reporting.units.show', compact('rpUnit', 'totalActions', 'totalActiveActions', 'totalActivities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpUnit $rpUnit)
    {
        $programs = RpProgram::where('is_active', true)
            ->with('component')
            ->orderBy('name')
            ->get(['rp_programs_id', 'name', 'code']);
        
        $unitTypes = [
            'department' => 'Department',
            'division' => 'Division',
            'section' => 'Section',
            'team' => 'Team',
            'working_group' => 'Working Group',
            'committee' => 'Committee',
            'task_force' => 'Task Force'
        ];
        
        return view('reporting.units.edit', compact('rpUnit', 'programs', 'unitTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpUnit $rpUnit)
    {
        $validated = $request->validate([
            'program_id' => 'required|uuid|exists:rp_programs,rp_programs_id',
            'external_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_units,code,' . $rpUnit->rp_units_id . ',rp_units_id',
            'unit_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $rpUnit->update($validated);

        return redirect()->route('reporting.units.show', $rpUnit)
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpUnit $rpUnit)
    {
        $rpUnit->delete();

        return redirect()->route('reporting.units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    /**
     * Get units by program ID (for AJAX requests)
     */
    public function getByProgram($programId)
    {
        $units = RpUnit::where('program_id', $programId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['rp_units_id', 'name', 'code', 'unit_type']);
        
        return response()->json($units);
    }

    /**
     * Get unit statistics
     */
    public function getStatistics($id)
    {
        $unit = RpUnit::with([
            'program.component',
            'actions.activities',
            'actions.targetActions'
        ])->findOrFail($id);
        
        $statistics = [
            'total_actions' => $unit->actions->count(),
            'active_actions' => $unit->actions->where('is_active', true)->count(),
            'total_activities' => $unit->actions->flatMap->activities->count(),
            'status_summary' => [
                'planned' => $unit->actions->where('status', 'planned')->count(),
                'ongoing' => $unit->actions->where('status', 'ongoing')->count(),
                'completed' => $unit->actions->where('status', 'completed')->count(),
                'cancelled' => $unit->actions->where('status', 'cancelled')->count(),
            ]
        ];
        
        return response()->json($statistics);
    }

    /**
     * Get units by type
     */
    public function getByType($type)
    {
        $units = RpUnit::where('unit_type', $type)
            ->where('is_active', true)
            ->with('program.component')
            ->orderBy('name')
            ->get();
        
        return response()->json($units);
    }

    /**
     * Toggle unit active status
     */
    public function toggleStatus($id)
    {
        $unit = RpUnit::findOrFail($id);
        $unit->is_active = !$unit->is_active;
        $unit->save();
        
        $status = $unit->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Unit {$status} successfully.");
    }
}