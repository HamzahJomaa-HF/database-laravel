<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpIndicator;

class ReportingIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpIndicator::query();
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->has('indicator_type')) {
            $query->where('indicator_type', $request->indicator_type);
        }
        
        if ($request->has('frequency')) {
            $query->where('frequency', $request->frequency);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('indicator_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('data_source', 'like', "%{$search}%");
            });
        }
        
        $indicators = $query->orderBy('indicator_code')->paginate(20);
        
        $indicatorTypes = RpIndicator::distinct()->pluck('indicator_type')->filter()->values();
        $frequencies = RpIndicator::distinct()->pluck('frequency')->filter()->values();
        $unitsOfMeasure = RpIndicator::distinct()->pluck('unit_of_measure')->filter()->values();
        
        return view('reporting.indicators.index', compact('indicators', 'indicatorTypes', 'frequencies', 'unitsOfMeasure'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $indicatorTypes = ['quantitative', 'qualitative', 'output', 'outcome', 'impact', 'process', 'efficiency', 'effectiveness'];
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'biannually', 'annually', 'on_demand', 'continuous'];
        $unitsOfMeasure = ['number', 'percentage', 'ratio', 'currency', 'days', 'hours', 'people', 'units', 'other'];
        
        return view('reporting.indicators.create', compact('indicatorTypes', 'frequencies', 'unitsOfMeasure'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|uuid',
            'indicator_code' => 'required|string|unique:rp_indicators,indicator_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'indicator_type' => 'nullable|string',
            'unit_of_measure' => 'nullable|string',
            'baseline_value' => 'nullable|numeric',
            'baseline_date' => 'nullable|date',
            'target_value' => 'nullable|numeric',
            'target_date' => 'nullable|date',
            'data_source' => 'nullable|string|max:255',
            'frequency' => 'nullable|string',
            'calculation_method' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Validate that if target_value is provided, target_date should also be provided
        if ($validated['target_value'] && !$validated['target_date']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['target_date' => 'Target date is required when target value is provided.']);
        }

        // Validate that if baseline_value is provided, baseline_date should also be provided
        if ($validated['baseline_value'] && !$validated['baseline_date']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['baseline_date' => 'Baseline date is required when baseline value is provided.']);
        }

        $indicator = RpIndicator::create($validated);

        return redirect()->route('reporting.indicators.index')
            ->with('success', 'Indicator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpIndicator $rpIndicator)
    {
        $rpIndicator->load(['activityAssignments.activity.action.unit.program.component']);
        
        // Get statistics
        $totalAssignments = $rpIndicator->activityAssignments->count();
        $activeActivities = $rpIndicator->activityAssignments->where('activity.is_active', true)->count();
        
        return view('reporting.indicators.show', compact('rpIndicator', 'totalAssignments', 'activeActivities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpIndicator $rpIndicator)
    {
        $indicatorTypes = ['quantitative', 'qualitative', 'output', 'outcome', 'impact', 'process', 'efficiency', 'effectiveness'];
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'biannually', 'annually', 'on_demand', 'continuous'];
        $unitsOfMeasure = ['number', 'percentage', 'ratio', 'currency', 'days', 'hours', 'people', 'units', 'other'];
        
        return view('reporting.indicators.edit', compact('rpIndicator', 'indicatorTypes', 'frequencies', 'unitsOfMeasure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpIndicator $rpIndicator)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|uuid',
            'indicator_code' => 'required|string|unique:rp_indicators,indicator_code,' . $rpIndicator->rp_indicators_id . ',rp_indicators_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'indicator_type' => 'nullable|string',
            'unit_of_measure' => 'nullable|string',
            'baseline_value' => 'nullable|numeric',
            'baseline_date' => 'nullable|date',
            'target_value' => 'nullable|numeric',
            'target_date' => 'nullable|date',
            'data_source' => 'nullable|string|max:255',
            'frequency' => 'nullable|string',
            'calculation_method' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Validate that if target_value is provided, target_date should also be provided
        if ($validated['target_value'] && !$validated['target_date']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['target_date' => 'Target date is required when target value is provided.']);
        }

        // Validate that if baseline_value is provided, baseline_date should also be provided
        if ($validated['baseline_value'] && !$validated['baseline_date']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['baseline_date' => 'Baseline date is required when baseline value is provided.']);
        }

        $rpIndicator->update($validated);

        return redirect()->route('reporting.indicators.show', $rpIndicator)
            ->with('success', 'Indicator updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpIndicator $rpIndicator)
    {
        $rpIndicator->delete();

        return redirect()->route('reporting.indicators.index')
            ->with('success', 'Indicator deleted successfully.');
    }

    /**
     * Get indicator by code
     */
    public function getByCode($code)
    {
        $indicator = RpIndicator::where('indicator_code', $code)
            ->where('is_active', true)
            ->first();
        
        if (!$indicator) {
            return response()->json(['error' => 'Indicator not found'], 404);
        }
        
        return response()->json($indicator);
    }

    /**
     * Get indicators by type
     */
    public function getByType($type)
    {
        $indicators = RpIndicator::where('indicator_type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($indicators);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $indicator = RpIndicator::findOrFail($id);
        $indicator->is_active = !$indicator->is_active;
        $indicator->save();
        
        $status = $indicator->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Indicator {$status} successfully.");
    }

    /**
     * Get indicator performance statistics
     */
    public function getPerformanceStatistics($id)
    {
        $indicator = RpIndicator::with(['activityAssignments.activity'])->findOrFail($id);
        
        $assignments = $indicator->activityAssignments;
        
        $statistics = [
            'total_assignments' => $assignments->count(),
            'active_assignments' => $assignments->where('activity.is_active', true)->count(),
            'baseline_value' => $indicator->baseline_value,
            'target_value' => $indicator->target_value,
            'target_date' => $indicator->target_date,
            'progress_needed' => $indicator->target_value && $indicator->baseline_value 
                ? $indicator->target_value - $indicator->baseline_value 
                : null,
            'time_remaining' => $indicator->target_date 
                ? now()->diffInDays($indicator->target_date, false) 
                : null
        ];
        
        return response()->json($statistics);
    }

    /**
     * Calculate indicator achievement
     */
    public function calculateAchievement($id)
    {
        $indicator = RpIndicator::with(['activityAssignments'])->findOrFail($id);
        
        if (!$indicator->baseline_value || !$indicator->target_value) {
            return response()->json([
                'achievement_percentage' => 0,
                'current_value' => 0,
                'progress' => 0
            ]);
        }
        
        // Calculate total achieved value from all activity assignments
        $totalAchieved = $indicator->activityAssignments->sum('achieved_value');
        $totalTarget = $indicator->activityAssignments->sum('target_value');
        
        // If no activity assignments, use indicator's own values
        if ($totalTarget <= 0) {
            $currentValue = $indicator->baseline_value; // This should be updated with actual data
            $achievement = $indicator->target_value > 0 
                ? (($currentValue - $indicator->baseline_value) / ($indicator->target_value - $indicator->baseline_value)) * 100 
                : 0;
        } else {
            $achievement = ($totalAchieved / $totalTarget) * 100;
        }
        
        return response()->json([
            'achievement_percentage' => $achievement,
            'baseline_value' => $indicator->baseline_value,
            'target_value' => $indicator->target_value,
            'total_achieved' => $totalAchieved,
            'total_target' => $totalTarget
        ]);
    }

    /**
     * Import indicators from CSV/Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls'
        ]);
        
        // TODO: Implement CSV/Excel import logic
        
        return redirect()->route('reporting.indicators.index')
            ->with('success', 'Indicators imported successfully.');
    }

    /**
     * Export indicators
     */
    public function export(Request $request)
    {
        $indicators = RpIndicator::all();
        
        return response()->streamDownload(function() use ($indicators) {
            echo "Code,Name,Type,Unit,Baseline Value,Baseline Date,Target Value,Target Date,Data Source,Frequency,Status\n";
            foreach ($indicators as $indicator) {
                echo implode(',', [
                    $indicator->indicator_code,
                    $indicator->name,
                    $indicator->indicator_type,
                    $indicator->unit_of_measure,
                    $indicator->baseline_value,
                    $indicator->baseline_date,
                    $indicator->target_value,
                    $indicator->target_date,
                    $indicator->data_source,
                    $indicator->frequency,
                    $indicator->is_active ? 'Active' : 'Inactive'
                ]) . "\n";
            }
        }, 'indicators_' . date('Y-m-d') . '.csv');
    }

    /**
     * Get indicator assignments
     */
    public function getAssignments($id)
    {
        $indicator = RpIndicator::with([
            'activityAssignments.activity.action.unit.program.component',
            'activityAssignments.activity'
        ])->findOrFail($id);
        
        return response()->json($indicator->activityAssignments);
    }

    /**
     * Get indicators with target dates approaching
     */
    public function getUpcomingTargets()
    {
        $indicators = RpIndicator::where('is_active', true)
            ->whereNotNull('target_date')
            ->whereDate('target_date', '>=', now())
            ->whereDate('target_date', '<=', now()->addDays(30))
            ->orderBy('target_date', 'asc')
            ->get();
        
        return response()->json($indicators);
    }
}