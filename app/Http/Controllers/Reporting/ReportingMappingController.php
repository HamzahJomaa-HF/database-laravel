<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpActivityMapping;
use App\Models\RpActivity;
use App\Models\Activity;

class ReportingMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RpActivityMapping::with(['rpActivity', 'mainActivity']);
        
        if ($request->has('rp_activity_id')) {
            $query->where('rp_activity_id', $request->rp_activity_id);
        }
        
        if ($request->has('external_type')) {
            $query->where('external_type', $request->external_type);
        }
        
        if ($request->has('mapping_type')) {
            $query->where('mapping_type', $request->mapping_type);
        }
        
        if ($request->has('sync_direction')) {
            $query->where('sync_direction', $request->sync_direction);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->has('sync_status')) {
            $query->where('sync_status', $request->sync_status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('external_activity_code', 'like', "%{$search}%")
                  ->orWhere('external_activity_id', 'like', "%{$search}%")
                  ->orWhere('external_system_name', 'like', "%{$search}%")
                  ->orWhere('mapping_notes', 'like', "%{$search}%")
                  ->orWhereHas('rpActivity', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }
        
        $mappings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $mappingTypes = ['direct', 'aggregated', 'partial', 'hierarchical', 'custom'];
        $syncDirections = ['one_way', 'two_way', 'read_only', 'write_only'];
        $externalTypes = ['activities', 'erp', 'crm', 'financial_system', 'donor_system', 'government_portal'];
        
        return view('reporting.mappings.index', compact('mappings', 'mappingTypes', 'syncDirections', 'externalTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rpActivities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $mainActivities = Activity::where('is_active', true)
            ->orderBy('name')
            ->get(['activity_id', 'name', 'code']);
        
        $mappingTypes = ['direct', 'aggregated', 'partial', 'hierarchical', 'custom'];
        $syncDirections = ['one_way', 'two_way', 'read_only', 'write_only'];
        $externalTypes = ['activities', 'erp', 'crm', 'financial_system', 'donor_system', 'government_portal'];
        $syncStatuses = ['pending', 'syncing', 'success', 'failed', 'partial'];
        
        return view('reporting.mappings.create', compact('rpActivities', 'mainActivities', 'mappingTypes', 'syncDirections', 'externalTypes', 'syncStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rp_activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'main_activity_id' => 'nullable|uuid|exists:activities,activity_id',
            'external_activity_id' => 'nullable|string',
            'external_activity_code' => 'nullable|string',
            'external_type' => 'required|string',
            'external_system_name' => 'nullable|string',
            'mapping_type' => 'required|string',
            'mapping_percentage' => 'nullable|numeric|between:0,100',
            'sync_direction' => 'required|string',
            'mapping_notes' => 'nullable|string',
            'sync_rules' => 'nullable|string',
            'mapped_date' => 'nullable|date',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'sync_pending' => 'boolean',
            'sync_status' => 'required|string'
        ]);

        // Validate that either main_activity_id or external_activity_id is provided
        if (empty($validated['main_activity_id']) && empty($validated['external_activity_id'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Either Main Activity ID or External Activity ID must be provided.']);
        }

        // Check for duplicate mappings
        if (!empty($validated['main_activity_id'])) {
            $exists = RpActivityMapping::where('rp_activity_id', $validated['rp_activity_id'])
                ->where('main_activity_id', $validated['main_activity_id'])
                ->where('external_type', $validated['external_type'])
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'This internal mapping already exists.']);
            }
        }

        if (!empty($validated['external_activity_id'])) {
            $exists = RpActivityMapping::where('rp_activity_id', $validated['rp_activity_id'])
                ->where('external_activity_id', $validated['external_activity_id'])
                ->where('external_type', $validated['external_type'])
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'This external mapping already exists.']);
            }
        }

        $mapping = RpActivityMapping::create($validated);

        return redirect()->route('reporting.mappings.index')
            ->with('success', 'Activity mapping created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpActivityMapping $rpActivityMapping)
    {
        $rpActivityMapping->load(['rpActivity.action.unit.program.component', 'mainActivity']);
        
        return view('reporting.mappings.show', compact('rpActivityMapping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpActivityMapping $rpActivityMapping)
    {
        $rpActivities = RpActivity::where('is_active', true)
            ->orderBy('name')
            ->get(['rp_activities_id', 'name', 'code']);
        
        $mainActivities = Activity::where('is_active', true)
            ->orderBy('name')
            ->get(['activity_id', 'name', 'code']);
        
        $mappingTypes = ['direct', 'aggregated', 'partial', 'hierarchical', 'custom'];
        $syncDirections = ['one_way', 'two_way', 'read_only', 'write_only'];
        $externalTypes = ['activities', 'erp', 'crm', 'financial_system', 'donor_system', 'government_portal'];
        $syncStatuses = ['pending', 'syncing', 'success', 'failed', 'partial'];
        
        return view('reporting.mappings.edit', compact('rpActivityMapping', 'rpActivities', 'mainActivities', 'mappingTypes', 'syncDirections', 'externalTypes', 'syncStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpActivityMapping $rpActivityMapping)
    {
        $validated = $request->validate([
            'rp_activity_id' => 'required|uuid|exists:rp_activities,rp_activities_id',
            'main_activity_id' => 'nullable|uuid|exists:activities,activity_id',
            'external_activity_id' => 'nullable|string',
            'external_activity_code' => 'nullable|string',
            'external_type' => 'required|string',
            'external_system_name' => 'nullable|string',
            'mapping_type' => 'required|string',
            'mapping_percentage' => 'nullable|numeric|between:0,100',
            'sync_direction' => 'required|string',
            'mapping_notes' => 'nullable|string',
            'sync_rules' => 'nullable|string',
            'mapped_date' => 'nullable|date',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'sync_pending' => 'boolean',
            'sync_status' => 'required|string'
        ]);

        // Validate that either main_activity_id or external_activity_id is provided
        if (empty($validated['main_activity_id']) && empty($validated['external_activity_id'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Either Main Activity ID or External Activity ID must be provided.']);
        }

        // Check for duplicate mappings (excluding current record)
        if (!empty($validated['main_activity_id'])) {
            $exists = RpActivityMapping::where('rp_activity_id', $validated['rp_activity_id'])
                ->where('main_activity_id', $validated['main_activity_id'])
                ->where('external_type', $validated['external_type'])
                ->where('rp_activity_mappings_id', '!=', $rpActivityMapping->rp_activity_mappings_id)
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'This internal mapping already exists.']);
            }
        }

        if (!empty($validated['external_activity_id'])) {
            $exists = RpActivityMapping::where('rp_activity_id', $validated['rp_activity_id'])
                ->where('external_activity_id', $validated['external_activity_id'])
                ->where('external_type', $validated['external_type'])
                ->where('rp_activity_mappings_id', '!=', $rpActivityMapping->rp_activity_mappings_id)
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'This external mapping already exists.']);
            }
        }

        $rpActivityMapping->update($validated);

        return redirect()->route('reporting.mappings.show', $rpActivityMapping)
            ->with('success', 'Activity mapping updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpActivityMapping $rpActivityMapping)
    {
        $rpActivityMapping->delete();

        return redirect()->route('reporting.mappings.index')
            ->with('success', 'Activity mapping deleted successfully.');
    }

    /**
     * Get mappings by RP activity ID
     */
    public function getByRpActivity($rpActivityId)
    {
        $mappings = RpActivityMapping::where('rp_activity_id', $rpActivityId)
            ->with(['mainActivity'])
            ->where('is_active', true)
            ->orderBy('mapped_date', 'desc')
            ->get();
        
        return response()->json($mappings);
    }

    /**
     * Get mappings by external activity ID
     */
    public function getByExternalActivity($externalActivityId, $externalType = null)
    {
        $query = RpActivityMapping::where('external_activity_id', $externalActivityId)
            ->with(['rpActivity']);
            
        if ($externalType) {
            $query->where('external_type', $externalType);
        }
        
        $mappings = $query->where('is_active', true)
            ->orderBy('mapped_date', 'desc')
            ->get();
        
        return response()->json($mappings);
    }

    /**
     * Sync a mapping
     */
    public function syncMapping($id)
    {
        $mapping = RpActivityMapping::findOrFail($id);
        
        // Update sync status
        $mapping->update([
            'sync_status' => 'syncing',
            'sync_pending' => false,
            'last_sync_at' => now()
        ]);
        
        // TODO: Implement actual sync logic based on mapping type and sync direction
        
        // Simulate sync completion
        $mapping->update([
            'sync_status' => 'success',
            'last_sync_result' => 'Sync completed successfully.'
        ]);
        
        return redirect()->back()
            ->with('success', 'Mapping synced successfully.');
    }

    /**
     * Bulk sync mappings
     */
    public function bulkSync(Request $request)
    {
        $validated = $request->validate([
            'mapping_ids' => 'required|array',
            'mapping_ids.*' => 'uuid|exists:rp_activity_mappings,rp_activity_mappings_id'
        ]);
        
        $mappings = RpActivityMapping::whereIn('rp_activity_mappings_id', $validated['mapping_ids'])->get();
        
        foreach ($mappings as $mapping) {
            // Update sync status
            $mapping->update([
                'sync_status' => 'syncing',
                'sync_pending' => false
            ]);
            
            // TODO: Implement actual sync logic
            
            // Simulate sync completion
            $mapping->update([
                'sync_status' => 'success',
                'last_sync_at' => now(),
                'last_sync_result' => 'Bulk sync completed successfully.'
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Bulk sync initiated for ' . count($mappings) . ' mappings.');
    }

    /**
     * Get mapping statistics
     */
    public function getStatistics()
    {
        $statistics = [
            'total_mappings' => RpActivityMapping::count(),
            'active_mappings' => RpActivityMapping::where('is_active', true)->count(),
            'pending_sync' => RpActivityMapping::where('sync_pending', true)->count(),
            'by_mapping_type' => RpActivityMapping::groupBy('mapping_type')->selectRaw('mapping_type, count(*) as count')->get(),
            'by_external_type' => RpActivityMapping::groupBy('external_type')->selectRaw('external_type, count(*) as count')->get(),
            'by_sync_direction' => RpActivityMapping::groupBy('sync_direction')->selectRaw('sync_direction, count(*) as count')->get(),
            'by_sync_status' => RpActivityMapping::groupBy('sync_status')->selectRaw('sync_status, count(*) as count')->get()
        ];
        
        return response()->json($statistics);
    }

    /**
     * Validate mapping
     */
    public function validateMapping($id)
    {
        $mapping = RpActivityMapping::with(['rpActivity', 'mainActivity'])->findOrFail($id);
        
        $validation = [
            'mapping_id' => $mapping->rp_activity_mappings_id,
            'is_valid' => true,
            'issues' => [],
            'warnings' => []
        ];
        
        // Check if mapping is still valid
        if ($mapping->valid_to && now()->greaterThan($mapping->valid_to)) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'Mapping has expired.';
        }
        
        // Check if RP activity exists and is active
        if (!$mapping->rpActivity || !$mapping->rpActivity->is_active) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'RP Activity is not active or does not exist.';
        }
        
        // Check if main activity exists (if mapped internally)
        if ($mapping->main_activity_id && (!$mapping->mainActivity || !$mapping->mainActivity->is_active)) {
            $validation['warnings'][] = 'Main Activity is not active or does not exist.';
        }
        
        // Check sync configuration
        if ($mapping->sync_direction === 'two_way' && empty($mapping->sync_rules)) {
            $validation['warnings'][] = 'Two-way sync configured but no sync rules defined.';
        }
        
        return response()->json($validation);
    }

    /**
     * Get mappings needing sync
     */
    public function getMappingsNeedingSync()
    {
        $mappings = RpActivityMapping::where('is_active', true)
            ->where(function($query) {
                $query->where('sync_pending', true)
                      ->orWhere('sync_status', 'failed')
                      ->orWhereNull('last_sync_at');
            })
            ->with(['rpActivity', 'mainActivity'])
            ->orderBy('last_sync_at', 'asc')
            ->get();
        
        return response()->json($mappings);
    }
}