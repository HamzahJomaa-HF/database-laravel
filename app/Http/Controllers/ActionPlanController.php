<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\RpComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ActionPlanController extends Controller
{
    /**
     * Display a listing of action plans with filtering.
     */
    public function index(Request $request)
    {
        try {
            // Start query builder
            $query = ActionPlan::query()
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

    /**
     * Bulk delete action plans.
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

            // Get action plans to delete
            $actionPlans = ActionPlan::whereIn('action_plan_id', $actionPlanIds)->get();
            
            $deletedCount = 0;
            $failedDeletions = [];

            // Delete each action plan and its associated file
            foreach ($actionPlans as $actionPlan) {
                if ($this->deleteActionPlanAndFile($actionPlan)) {
                    $deletedCount++;
                } else {
                    $failedDeletions[] = $actionPlan->title ?: $actionPlan->external_id;
                }
            }

            // Prepare response message
            $message = "Successfully deleted {$deletedCount} action plan(s).";
            
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
     * Delete a single action plan.
     */
    public function destroy($id)
    {
        try {
            $actionPlan = ActionPlan::findOrFail($id);

            if ($this->deleteActionPlanAndFile($actionPlan)) {
                return redirect()->route('action-plans.index')
                    ->with('success', 'Action plan deleted successfully.');
            } else {
                return redirect()->route('action-plans.index')
                    ->with('error', 'Failed to delete action plan or associated file.');
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
     * Helper method to delete action plan and its file.
     */
    private function deleteActionPlanAndFile(ActionPlan $actionPlan)
    {
        DB::beginTransaction();
        
        try {
            // Delete associated Excel file if it exists
            if ($actionPlan->excel_url) {
                $this->deleteExcelFile($actionPlan->excel_url);
            }

            // Delete the action plan record
            $actionPlan->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete action plan: ' . $e->getMessage(), [
                'action_plan_id' => $actionPlan->action_plan_id,
                'file_url' => $actionPlan->excel_url
            ]);
            return false;
        }
    }

    /**
     * Delete the Excel file from storage.
     */
    private function deleteExcelFile($fileUrl)
    {
        try {
            // Extract filename from URL
            $filename = basename($fileUrl);
            
            // Determine storage path based on your configuration
            // Adjust this based on where you store your Excel files
            
            // Example for local storage in 'action-plans' directory
            $storagePath = 'public/action-plans/' . $filename;
            
            // Example for S3 or other storage
            // $storagePath = 'action-plans/' . $filename;
            
            if (Storage::exists($storagePath)) {
                Storage::delete($storagePath);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to delete Excel file: ' . $e->getMessage(), [
                'file_url' => $fileUrl
            ]);
            return false;
        }
    }

    /**
     * Show import statistics (optional).
     */
    public function importStatistics()
    {
        $stats = [
            'total_action_plans' => ActionPlan::count(),
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
                'Component', 'Import Date', 'Excel File'
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
                    $plan->excel_filename
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
        $actionPlan = ActionPlan::with('component')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => $actionPlan,
            'import_stats' => $actionPlan->excel_metadata['import_results'] ?? null
        ]);
    }
}