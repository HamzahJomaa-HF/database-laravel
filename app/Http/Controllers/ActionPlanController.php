<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\RpComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ActionPlanController extends Controller
{
    // ============================================
    // INDEX & FILTERING METHODS
    // ============================================
    
    /**
     * Display a listing of action plans with filtering.
     */
    public function index(Request $request)
    {
        try {
            // Start query builder - only non-deleted records
            $query = ActionPlan::query()
                ->whereNull('deleted_at')
                ->with(['component' => function ($query) {
                    $query->select('rp_components_id', 'code', 'title');
                }])
                ->orderBy('excel_uploaded_at', 'desc');

            // Apply filters
            $query = $this->applyFilters($query, $request);
            
            // Get paginated results
            $actionPlans = $query->paginate(20)->withQueryString();

            // Check if any search/filter is applied
            $hasSearch = $this->hasSearchApplied($request);

            return view('action-plans.index', compact('actionPlans', 'hasSearch'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading action plans: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of soft deleted action plans.
     */
    public function trash(Request $request)
    {
        try {
            // Start query builder - only deleted records
            $query = ActionPlan::query()
                ->onlyTrashed()
                ->with(['component' => function ($query) {
                    $query->select('rp_components_id', 'code', 'title');
                }])
                ->orderBy('deleted_at', 'desc');

            // Apply filters to trash
            $query = $this->applyFilters($query, $request);
            
            // Get paginated results
            $actionPlans = $query->paginate(20)->withQueryString();

            return view('action-plans.trash', compact('actionPlans'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading trashed action plans: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Title search
        if ($request->filled('title')) {
            $searchTerm = '%' . $request->input('title') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                  ->orWhere('external_id', 'LIKE', $searchTerm);
            });
        }

        // External ID exact match
        if ($request->filled('external_id')) {
            $query->where('external_id', $request->input('external_id'));
        }

        // Import date range
        if ($request->filled('imported_from')) {
            $query->whereDate('excel_uploaded_at', '>=', $request->input('imported_from'));
        }
        if ($request->filled('imported_to')) {
            $query->whereDate('excel_uploaded_at', '<=', $request->input('imported_to'));
        }

        // Plan start date
        if ($request->filled('plan_start_date')) {
            $query->whereDate('start_date', '>=', $request->input('plan_start_date'));
        }

        // Plan end date
        if ($request->filled('plan_end_date')) {
            $query->whereDate('end_date', '<=', $request->input('plan_end_date'));
        }

        // Component filter (if you have component_id in action_plans)
        if ($request->filled('rp_components_id')) {
            $query->where('component_id', $request->input('rp_components_id'));
        }

        // Deleted date range filter (for trash view)
        if ($request->filled('deleted_from')) {
            $query->whereDate('deleted_at', '>=', $request->input('deleted_from'));
        }
        if ($request->filled('deleted_to')) {
            $query->whereDate('deleted_at', '<=', $request->input('deleted_to'));
        }

        return $query;
    }

    /**
     * Check if any search/filter is applied.
     */
    private function hasSearchApplied(Request $request)
    {
        $searchParams = [
            'title',
            'external_id',
            'imported_from',
            'imported_to',
            'plan_start_date',
            'plan_end_date',
            'rp_components_id'
        ];

        foreach ($searchParams as $param) {
            if ($request->filled($param)) {
                return true;
            }
        }

        return false;
    }

    // ============================================
    // FILE UPLOAD & IMPORT METHODS
    // ============================================
    
    /**
     * Upload and store Excel file (for your import process)
     */
    public function storeExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
                'title' => 'required|string|max:255',
                'external_id' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'rp_components_id' => 'nullable|exists:rp_components,rp_components_id',
            ]);
            
            // Get the uploaded file
            $file = $request->file('excel_file');
            $originalName = $file->getClientOriginalName();
            
            // Generate a safe, unique filename
            $timestamp = time();
            $safeName = preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);
            $filename = $timestamp . '_' . $safeName;
            
            // Define storage directory
            $directory = 'action_plans';
            
            // Ensure directory exists
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            
            // Store the file
            $path = $file->storeAs($directory, $filename, 'public');
            
            // Verify the file was saved
            if (!Storage::exists($path)) {
                throw new \Exception('Failed to save file to storage.');
            }
            
            // Create action plan with proper file paths
            $actionPlan = ActionPlan::create([
                'title' => $request->title,
                'external_id' => $request->external_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'rp_components_id' => $request->rp_components_id,
                'excel_filename' => $filename,
               'excel_path' => 'public/action_plans/' . $filename, 
                'excel_metadata' => [
                    'original_name' => $originalName,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toDateTimeString(),
                    'storage_path' => storage_path('app/public/action_plans/' . $filename),
                ],
                'excel_uploaded_at' => now(),
            ]);
            
            return redirect()->route('action-plans.index')
                ->with('success', 'Action plan imported successfully! File saved and ready for download.');
                
        } catch (\Exception $e) {
            Log::error('Error importing action plan: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error importing action plan: ' . $e->getMessage());
        }
    }

    // ============================================
    // FILE DOWNLOAD METHODS
    // ============================================
    
    /**
     * Download the original Excel file
     */
    public function download(ActionPlan $actionPlan): BinaryFileResponse
    {
        // Check if file exists in database record
        if (!$actionPlan->excel_path && !$actionPlan->excel_filename) {
            abort(404, 'No file associated with this action plan.');
        }
        
        // Try to find the file in storage
        $filePath = $this->findFileInStorage($actionPlan);
        
        if (!$filePath) {
            abort(404, 'Excel file not found in storage.');
        }
        
        // Get the original filename for download
        $originalName = $this->getOriginalFilename($actionPlan);
        
        // Return the file as a download
        return response()->download(
            storage_path('app/' . $filePath),
            $originalName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $originalName . '"'
            ]
        );
    }

    /**
     * Helper: Find file in storage
     */
    private function findFileInStorage(ActionPlan $actionPlan): ?string
    {
        $searchPaths = [];
        
        
        // First, try the stored path if it exists
        if ($actionPlan->excel_path) {
            $searchPaths[] = $actionPlan->excel_path;
        }
        
        // Common storage locations to check
        if ($actionPlan->excel_filename) {
            $searchPaths = array_merge($searchPaths, [
                'public/action_plans/' . $actionPlan->excel_filename, 
                'action_plans/' . $actionPlan->excel_filename,
                'uploads/' . $actionPlan->excel_filename,
                $actionPlan->excel_filename, // Direct in storage root
            ]);
        }
        
        // Check each location
        foreach ($searchPaths as $path) {
            if (Storage::exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    /**
     * Helper: Get original filename for download
     */
    private function getOriginalFilename(ActionPlan $actionPlan): string
    {
        // Try to get from metadata first
        if ($actionPlan->excel_metadata && isset($actionPlan->excel_metadata['original_name'])) {
            return $actionPlan->excel_metadata['original_name'];
        }
        
        // Fallback to stored filename
        return $actionPlan->excel_filename ?? 'action-plan.xlsx';
    }

    // ============================================
    // SOFT DELETE METHODS
    // ============================================
    
    /**
     * Bulk soft delete action plans.
     */
    public function bulkDestroy(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'action_plan_ids' => 'required|string'
            ]);

            // Convert comma-separated string to array
            $actionPlanIds = explode(',', $request->input('action_plan_ids'));
            
            if (empty($actionPlanIds)) {
                return redirect()->back()
                    ->with('error', 'No action plans selected for deletion.');
            }

            // Get action plans to soft delete
            $actionPlans = ActionPlan::whereIn('action_plan_id', $actionPlanIds)->get();
            
            $deletedCount = 0;
            $failedDeletions = [];

            // Soft delete each action plan
            foreach ($actionPlans as $actionPlan) {
                if ($this->softDeleteActionPlan($actionPlan)) {
                    $deletedCount++;
                } else {
                    $failedDeletions[] = $actionPlan->title ?: $actionPlan->external_id;
                }
            }

            // Prepare response message
            $message = "Successfully moved {$deletedCount} action plan(s) to trash.";
            
            if (!empty($failedDeletions)) {
                $failedList = implode(', ', $failedDeletions);
                $message .= " Failed to delete: {$failedList}";
                
                if ($deletedCount > 0) {
                    return redirect()->route('action-plans.index')
                        ->with('warning', $message);
                } else {
                    return redirect()->route('action-plans.index')
                        ->with('error', $message);
                }
            }

            return redirect()->route('action-plans.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error during bulk deletion: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a single action plan.
     */
    public function destroy($id)
    {
        try {
            $actionPlan = ActionPlan::findOrFail($id);

            if ($this->softDeleteActionPlan($actionPlan)) {
                return redirect()->route('action-plans.index')
                    ->with('success', 'Action plan moved to trash successfully.');
            } else {
                return redirect()->route('action-plans.index')
                    ->with('error', 'Failed to delete action plan.');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('action-plans.index')
                ->with('error', 'Action plan not found.');

        } catch (\Exception $e) {
            return redirect()->route('action-plans.index')
                ->with('error', 'Error deleting action plan: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted action plan.
     */
    public function restore($id)
    {
        try {
            $actionPlan = ActionPlan::onlyTrashed()->findOrFail($id);
            
            $actionPlan->restore();
            
            return redirect()->route('action-plans.trash')
                ->with('success', 'Action plan restored successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('action-plans.trash')
                ->with('error', 'Action plan not found in trash.');

        } catch (\Exception $e) {
            return redirect()->route('action-plans.trash')
                ->with('error', 'Error restoring action plan: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a soft deleted action plan.
     */
    public function forceDestroy($id)
    {
        try {
            $actionPlan = ActionPlan::onlyTrashed()->findOrFail($id);
            
            if ($this->permanentlyDeleteActionPlan($actionPlan)) {
                return redirect()->route('action-plans.trash')
                    ->with('success', 'Action plan permanently deleted.');
            } else {
                return redirect()->route('action-plans.trash')
                    ->with('error', 'Failed to permanently delete action plan.');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('action-plans.trash')
                ->with('error', 'Action plan not found in trash.');

        } catch (\Exception $e) {
            return redirect()->route('action-plans.trash')
                ->with('error', 'Error permanently deleting action plan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk restore action plans from trash.
     */
    public function bulkRestore(Request $request)
    {
        try {
            $request->validate([
                'action_plan_ids' => 'required|string'
            ]);

            $actionPlanIds = explode(',', $request->input('action_plan_ids'));
            
            if (empty($actionPlanIds)) {
                return redirect()->back()
                    ->with('error', 'No action plans selected for restoration.');
            }

            $restoredCount = ActionPlan::onlyTrashed()
                ->whereIn('action_plan_id', $actionPlanIds)
                ->restore();

            return redirect()->route('action-plans.trash')
                ->with('success', "Successfully restored {$restoredCount} action plan(s).");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error during bulk restoration: ' . $e->getMessage());
        }
    }

    /**
     * Bulk permanently delete action plans from trash.
     */
    public function bulkForceDestroy(Request $request)
    {
        try {
            $request->validate([
                'action_plan_ids' => 'required|string'
            ]);

            $actionPlanIds = explode(',', $request->input('action_plan_ids'));
            
            if (empty($actionPlanIds)) {
                return redirect()->back()
                    ->with('error', 'No action plans selected for permanent deletion.');
            }

            $actionPlans = ActionPlan::onlyTrashed()
                ->whereIn('action_plan_id', $actionPlanIds)
                ->get();
            
            $deletedCount = 0;
            $failedDeletions = [];

            foreach ($actionPlans as $actionPlan) {
                if ($this->permanentlyDeleteActionPlan($actionPlan)) {
                    $deletedCount++;
                } else {
                    $failedDeletions[] = $actionPlan->title ?: $actionPlan->external_id;
                }
            }

            $message = "Successfully permanently deleted {$deletedCount} action plan(s).";
            
            if (!empty($failedDeletions)) {
                $failedList = implode(', ', $failedDeletions);
                $message .= " Failed to delete: {$failedList}";
                
                if ($deletedCount > 0) {
                    return redirect()->route('action-plans.trash')
                        ->with('warning', $message);
                } else {
                    return redirect()->route('action-plans.trash')
                        ->with('error', $message);
                }
            }

            return redirect()->route('action-plans.trash')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error during bulk permanent deletion: ' . $e->getMessage());
        }
    }

    /**
     * Empty the trash (permanently delete all soft deleted records).
     */
    public function emptyTrash()
    {
        try {
            $actionPlans = ActionPlan::onlyTrashed()->get();
            
            $deletedCount = 0;
            $failedDeletions = [];

            foreach ($actionPlans as $actionPlan) {
                if ($this->permanentlyDeleteActionPlan($actionPlan)) {
                    $deletedCount++;
                } else {
                    $failedDeletions[] = $actionPlan->title ?: $actionPlan->external_id;
                }
            }

            $message = "Successfully emptied trash. Permanently deleted {$deletedCount} action plan(s).";
            
            if (!empty($failedDeletions)) {
                $failedList = implode(', ', $failedDeletions);
                $message .= " Failed to delete: {$failedList}";
                
                if ($deletedCount > 0) {
                    return redirect()->route('action-plans.trash')
                        ->with('warning', $message);
                } else {
                    return redirect()->route('action-plans.trash')
                        ->with('error', $message);
                }
            }

            return redirect()->route('action-plans.trash')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('action-plans.trash')
                ->with('error', 'Error emptying trash: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to soft delete action plan.
     */
    private function softDeleteActionPlan(ActionPlan $actionPlan)
    {
        try {
            $actionPlan->delete();
            Log::info('Soft deleted action plan', [
                'action_plan_id' => $actionPlan->action_plan_id,
                'title' => $actionPlan->title,
                'deleted_at' => now()->toDateTimeString()
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to soft delete action plan: ' . $e->getMessage(), [
                'action_plan_id' => $actionPlan->action_plan_id,
                'title' => $actionPlan->title
            ]);
            return false;
        }
    }

    /**
     * Helper method to permanently delete action plan and its file.
     */
    private function permanentlyDeleteActionPlan(ActionPlan $actionPlan)
    {
        DB::beginTransaction();
        
        try {
            // Delete associated Excel file if it exists
            if ($actionPlan->excel_path && Storage::exists($actionPlan->excel_path)) {
                Storage::delete($actionPlan->excel_path);
                Log::info('Deleted file during permanent deletion: ' . $actionPlan->excel_path);
            }

            // Force delete the action plan record
            $actionPlan->forceDelete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete action plan: ' . $e->getMessage(), [
                'action_plan_id' => $actionPlan->action_plan_id,
                'excel_path' => $actionPlan->excel_path
            ]);
            return false;
        }
    }

    // ============================================
    // DEBUG & UTILITY METHODS
    // ============================================
    
    /**
     * Fix file paths for existing action plans
     */
    public function fixFilePaths()
    {
        $actionPlans = ActionPlan::whereNotNull('excel_filename')->get();
        $fixedCount = 0;
        $missingFiles = [];
        
        foreach ($actionPlans as $plan) {
            // If excel_path is empty, try to determine it
            if (empty($plan->excel_path)) {
                $foundPath = $this->findFileInStorage($plan);
                
                if ($foundPath) {
                    $plan->excel_path = $foundPath;
                    $plan->save();
                    $fixedCount++;
                } else {
                    $missingFiles[] = [
                        'id' => $plan->action_plan_id,
                        'title' => $plan->title,
                        'filename' => $plan->excel_filename,
                    ];
                }
            }
        }
        
        return response()->json([
            'message' => 'Fixed ' . $fixedCount . ' action plans.',
            'missing_files' => $missingFiles,
            'total_checked' => $actionPlans->count()
        ]);
    }
    
    /**
     * View file info (for debugging)
     */
    public function fileInfo($id)
    {
        $actionPlan = ActionPlan::withTrashed()->findOrFail($id);
        
        $fileInfo = [
            'database' => [
                'excel_path' => $actionPlan->excel_path,
                'excel_filename' => $actionPlan->excel_filename,
                'excel_metadata' => $actionPlan->excel_metadata,
                'deleted_at' => $actionPlan->deleted_at,
                'is_soft_deleted' => !is_null($actionPlan->deleted_at),
            ],
            'storage' => [],
        ];
        
        // Check storage
        if ($actionPlan->excel_path && Storage::exists($actionPlan->excel_path)) {
            $fileInfo['storage']['primary_path'] = [
                'exists' => true,
                'path' => $actionPlan->excel_path,
                'size' => Storage::size($actionPlan->excel_path),
                'last_modified' => Storage::lastModified($actionPlan->excel_path),
            ];
        }
        
        return response()->json($fileInfo);
    }

    // ============================================
    // ADDITIONAL FEATURES
    // ============================================
    
    /**
     * Show import statistics (optional).
     */
    public function importStatistics()
    {
        $stats = [
            'total_action_plans' => ActionPlan::count(),
            'active_action_plans' => ActionPlan::whereNull('deleted_at')->count(),
            'deleted_action_plans' => ActionPlan::onlyTrashed()->count(),
            'recent_uploads' => ActionPlan::whereDate('excel_uploaded_at', '>=', now()->subDays(7))->count(),
            'by_component' => ActionPlan::with('component')
                ->select('rp_components_id', DB::raw('count(*) as count'))
                ->groupBy('rp_components_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->component->code ?? 'No Component' => $item->count];
                }),
        ];

        return view('action-plans.statistics', compact('stats'));
    }

    /**
     * Export action plans data (optional).
     */
    public function export(Request $request)
    {
        $query = ActionPlan::query()->with('component');
        $query = $this->applyFilters($query, $request);
        
        $actionPlans = $query->get();
        
        // Return CSV or Excel file
        // You can use Laravel Excel package or simple CSV
        $filename = 'action-plans-export-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($actionPlans) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID', 'Title', 'External ID', 'Start Date', 'End Date', 
                'Component', 'Import Date', 'Excel File', 'Deleted At'
            ]);
            
            // Data rows
            foreach ($actionPlans as $plan) {
                fputcsv($file, [
                    $plan->action_plan_id,
                    $plan->title,
                    $plan->external_id,
                    $plan->start_date,
                    $plan->end_date,
                    $plan->component->code ?? '',
                    $plan->excel_uploaded_at,
                    $plan->excel_filename,
                    $plan->deleted_at ?? 'Active'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get action plan details via AJAX (optional, for modal).
     */
    public function getDetails($id)
    {
        $actionPlan = ActionPlan::with(['component', 'deletedBy'])
            ->withTrashed()
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => $actionPlan,
            'import_stats' => $actionPlan->excel_metadata['import_results'] ?? null,
            'is_deleted' => !is_null($actionPlan->deleted_at),
            'deleted_at_formatted' => $actionPlan->deleted_at ? $actionPlan->deleted_at->format('Y-m-d H:i:s') : null
        ]);
    }
}