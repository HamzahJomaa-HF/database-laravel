<?php

namespace App\Http\Controllers;

use App\Models\ActivityFinancial;
use App\Models\Activity;
use App\Models\User;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FinancialController extends Controller
{
    /**
     * Display a listing of financial records.
     */
    public function index(Request $request)
    {
        // Medical records have dedicated pages; redirect away from the generic filter view
        if ($request->get('financial_type') === 'medical') {
            return redirect()->route('financials.medical.medicine');
        }

        $query = ActivityFinancial::with(['activity', 'user', 'cop'])
            ->orderBy('created_at', 'desc');

        // Filter by financial type
        if ($request->filled('financial_type')) {
            $query->where('financial_type', $request->financial_type);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by activity
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('tx_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tx_date', '<=', $request->end_date);
        }

        // Amount range filter
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Global search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('external_id', 'ilike', "%{$search}%")
                  ->orWhere('notes', 'ilike', "%{$search}%")
                  ->orWhereHas('activity', function ($activityQuery) use ($search) {
                      $activityQuery->where('activity_title_en', 'ilike', "%{$search}%")
                                    ->orWhere('activity_title_ar', 'ilike', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'ilike', "%{$search}%")
                                ->orWhere('last_name', 'ilike', "%{$search}%")
                                ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        // Activity-specific search
        if ($request->filled('activity_search')) {
            $activitySearch = $request->activity_search;
            $query->whereHas('activity', function ($activityQuery) use ($activitySearch) {
                $activityQuery->where('activity_title_en', 'ilike', "%{$activitySearch}%")
                              ->orWhere('activity_title_ar', 'ilike', "%{$activitySearch}%");
            });
        }

        // User-specific search
        if ($request->filled('user_search')) {
            $userSearch = $request->user_search;
            $query->whereHas('user', function ($userQuery) use ($userSearch) {
                $userQuery->where('first_name', 'ilike', "%{$userSearch}%")
                          ->orWhere('last_name', 'ilike', "%{$userSearch}%")
                          ->orWhere('email', 'ilike', "%{$userSearch}%");
            });
        }

        // Handle pagination
        $perPage = $request->get('per_page', 15);
        $financials = $query->paginate($perPage);

        // --- Totals (across all filtered records, not just current page) ---
        $totalsQuery = ActivityFinancial::query();

        if ($request->filled('financial_type'))   $totalsQuery->where('financial_type', $request->financial_type);
        if ($request->filled('payment_status'))   $totalsQuery->where('payment_status', $request->payment_status);
        if ($request->filled('start_date'))       $totalsQuery->whereDate('tx_date', '>=', $request->start_date);
        if ($request->filled('end_date'))         $totalsQuery->whereDate('tx_date', '<=', $request->end_date);
        if ($request->filled('min_amount'))       $totalsQuery->where('amount', '>=', $request->min_amount);
        if ($request->filled('max_amount'))       $totalsQuery->where('amount', '<=', $request->max_amount);
        if ($request->filled('activity_search')) {
            $totalsQuery->whereHas('activity', fn($q) => $q->where('activity_title_en', 'ilike', "%{$request->activity_search}%")->orWhere('activity_title_ar', 'ilike', "%{$request->activity_search}%"));
        }
        if ($request->filled('user_search')) {
            $totalsQuery->whereHas('user', fn($q) => $q->where('first_name', 'ilike', "%{$request->user_search}%")->orWhere('last_name', 'ilike', "%{$request->user_search}%"));
        }

        $summaryRaw = (clone $totalsQuery)->selectRaw("
            COUNT(*) as total_count,
            COALESCE(SUM(amount), 0) as grand_total,
            COALESCE(SUM(CASE WHEN financial_type = 'omt' THEN amount ELSE 0 END), 0) as omt_total,
            COALESCE(SUM(CASE WHEN financial_type = 'medical' THEN amount ELSE 0 END), 0) as medical_total,
            COALESCE(SUM(CASE WHEN financial_type = 'education' THEN amount ELSE 0 END), 0) as education_total,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END), 0) as paid_total,
            COALESCE(SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END), 0) as pending_total,
            COALESCE(SUM(CASE WHEN payment_status = 'partial' THEN amount ELSE 0 END), 0) as partial_total,
            COALESCE(SUM(CASE WHEN payment_status = 'overdue' THEN amount ELSE 0 END), 0) as overdue_total,
            COUNT(CASE WHEN financial_type = 'omt' THEN 1 END) as omt_count,
            COUNT(CASE WHEN financial_type = 'medical' THEN 1 END) as medical_count,
            COUNT(CASE WHEN financial_type = 'education' THEN 1 END) as education_count
        ")->first();

        $totals = [
            'count'      => $summaryRaw->total_count ?? 0,
            'grand'      => (float) ($summaryRaw->grand_total ?? 0),
            'omt'        => (float) ($summaryRaw->omt_total ?? 0),
            'medical'    => (float) ($summaryRaw->medical_total ?? 0),
            'education'  => (float) ($summaryRaw->education_total ?? 0),
            'paid'       => (float) ($summaryRaw->paid_total ?? 0),
            'pending'    => (float) ($summaryRaw->pending_total ?? 0),
            'partial'    => (float) ($summaryRaw->partial_total ?? 0),
            'overdue'    => (float) ($summaryRaw->overdue_total ?? 0),
            'omt_count'       => $summaryRaw->omt_count ?? 0,
            'medical_count'   => $summaryRaw->medical_count ?? 0,
            'education_count' => $summaryRaw->education_count ?? 0,
        ];
        // --- End Totals ---

        // Get data for filter dropdowns
        $activities = Activity::orderBy('activity_title_en')
            ->limit(100)
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);

        $users = User::orderBy('first_name')
            ->limit(100)
            ->get(['user_id', 'first_name', 'last_name', 'email']);

        // Check if request expects JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $financials,
                'totals' => $totals,
                'filters' => [
                    'activities' => $activities,
                    'users' => $users,
                ]
            ]);
        }

        return view('financials.index', compact('financials', 'activities', 'users', 'totals'));
    }

    /**
     * Show the form for creating a new financial record.
     */
    public function create()
    {
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        
        return view('financials.create', compact('activities', 'users', 'cops'));
    }

    /**
     * Store a newly created financial record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|uuid|exists:activities,activity_id',
            'user_id' => 'required|uuid|exists:users,user_id',
            'cop_id' => 'nullable|uuid|exists:cops,cop_id',
            'financial_type' => 'required|in:omt,medical,education',
            'amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid,overdue',
            'tx_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Build financial_data based on type
        $financialData = $this->buildFinancialData($request);

        try {
            DB::beginTransaction();

            $financial = ActivityFinancial::create([
                'activity_financial_id' => (string) Str::uuid(),
                'activity_id' => $validated['activity_id'],
                'user_id' => $validated['user_id'],
                'cop_id' => $validated['cop_id'] ?? null,
                'financial_type' => $validated['financial_type'],
                'amount' => $validated['amount'] ?? null,
                'payment_status' => $validated['payment_status'] ?? 'pending',
                'tx_date' => $request->tx_date ?? null,
                'financial_data' => $financialData,
                'notes' => $request->notes ?? null,
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Financial record created successfully!',
                    'data' => $financial->load(['activity', 'user', 'cop'])
                ], 201);
            }

            return redirect()->route('financials.index')
                ->with('success', 'Financial record created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create financial record: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create financial record: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to create financial record: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified financial record.
     */
    public function show($id)
    {
        $financial = ActivityFinancial::with(['activity', 'user', 'cop'])->findOrFail($id);
        return view('financials.show', compact('financial'));
    }

    /**
     * Show the form for editing the specified financial record.
     */
    public function edit($id)
    {
        $financial = ActivityFinancial::findOrFail($id);
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        
        return view('financials.edit', compact('financial', 'activities', 'users', 'cops'));
    }

    /**
     * Update financial record via AJAX (for popup edit)
     */
    public function update(Request $request, $id)
    {
        $financial = ActivityFinancial::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid,overdue',
            'tx_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'financial_data' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $financial->update([
                'amount' => $validated['amount'] ?? $financial->amount,
                'payment_status' => $validated['payment_status'] ?? $financial->payment_status,
                'tx_date' => $validated['tx_date'] ?? $financial->tx_date,
                'notes' => $validated['notes'] ?? $financial->notes,
            ]);

            // Update financial_data if provided
            if (!empty($validated['financial_data'])) {
                $mergedData = array_merge($financial->financial_data ?? [], $validated['financial_data']);
                $financial->financial_data = $mergedData;
                $financial->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Financial record updated successfully!',
                'data' => $financial
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified financial record.
     */
    public function destroy($id)
    {
        $financial = ActivityFinancial::findOrFail($id);

        try {
            DB::beginTransaction();
            $financial->delete();
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Financial record deleted successfully!'
                ]);
            }

            return redirect()->route('financials.index')
                ->with('success', 'Financial record deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete financial record: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete financial record: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete financial record: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove multiple financial records.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'financial_ids' => 'required|array',
            'financial_ids.*' => 'uuid|exists:activity_financials,activity_financial_id'
        ]);

        try {
            DB::beginTransaction();

            $count = ActivityFinancial::whereIn('activity_financial_id', $request->financial_ids)->count();
            ActivityFinancial::whereIn('activity_financial_id', $request->financial_ids)->delete();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$count} financial record(s) deleted successfully!"
                ]);
            }

            return redirect()->back()
                ->with('success', "{$count} financial record(s) deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk delete financial records: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete records: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete records: ' . $e->getMessage()]);
        }
    }

    /**
     * Build financial_data array from request.
     */
    private function buildFinancialData($request)
    {
        $data = [];

        if ($request->financial_type === 'omt') {
            $data = [
                'operational_cost' => $request->operational_cost,
                'personnel_cost' => $request->personnel_cost,
                'travel_cost' => $request->travel_cost,
                'equipment_cost' => $request->equipment_cost,
                'supplies_cost' => $request->supplies_cost,
                'training_cost' => $request->training_cost,
                'communication_cost' => $request->communication_cost,
            ];
        }

        if ($request->financial_type === 'medical') {
            // Check if it's medicine or hospital type
            $medicationType = $request->medication_type ?? 'medicine';
            
            if ($medicationType === 'medicine') {
                $data = [
                    'medication_type' => 'medicine',
                    'disease_type' => $request->disease_type,
                    'invoice_number' => $request->invoice_number,
                    'location' => $request->location,
                    'medicine_cost' => $request->medicine_cost,
                    'assistance_cost_after_pharmacy_discount' => $request->assistance_cost_after_pharmacy_discount,
                    'discount_percentage' => $request->discount_percentage,
                ];
            } else {
                $data = [
                    'medication_type' => 'hospital',
                    'operation_type' => $request->operation_type,
                    'description' => $request->description,
                    'location' => $request->location,
                    'operation_cost' => $request->operation_cost,
                    'medical_assistance' => $request->medical_assistance,
                    'residual_amount' => $request->residual_amount,
                    'covered_percentage' => $request->covered_percentage,
                    'other_assistance' => $request->other_assistance,
                ];
            }
        }

        if ($request->financial_type === 'education') {
            $data = [
                'scholarship_percentage' => $request->scholarship_percentage,
                'tuition_fees' => $request->tuition_fees,
                'books_supplies' => $request->books_supplies,
                'living_allowance' => $request->living_allowance,
                'student_count' => $request->student_count,
                'education_level' => $request->education_level,
                'institution_name' => $request->institution_name,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'registration_fees' => $request->registration_fees,
            ];
        }

        return array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * PowerBI-style visualization dashboard for financials
     */
    public function visualization(Request $request)
    {
        $page = $request->get('page_type', 'all');

        // ── Base stats (all records) ────────────────────────────────────────
        $byType = ActivityFinancial::selectRaw("financial_type, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('financial_type')->get()->keyBy('financial_type');

        $byStatus = ActivityFinancial::selectRaw("payment_status, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('payment_status')->get()->keyBy('payment_status');

        $monthly = ActivityFinancial::selectRaw("TO_CHAR(tx_date,'YYYY-MM') as month, COALESCE(SUM(amount),0) as total, COUNT(*) as cnt")
            ->whereNotNull('tx_date')
            ->groupBy('month')->orderBy('month')->get();

        // ── OMT: dynamic numeric JSONB breakdown ────────────────────────────
        $omtBreakdown = $this->getDynamicNumericBreakdown('omt', null);

        // ── OMT: total sent per activity ─────────────────────────────────────
        $omtByActivity = ActivityFinancial::where('financial_type','omt')
            ->join('activities', 'activity_financials.activity_id', '=', 'activities.activity_id')
            ->selectRaw("activities.activity_title_en as activity, COUNT(*) as cnt, COALESCE(SUM(activity_financials.amount),0) as total")
            ->groupBy('activities.activity_title_en')
            ->orderByDesc('total')
            ->get();

        // ── Medicine: disease grouping + dynamic numeric JSONB fields ────────
        $medicineByDisease = ActivityFinancial::where('financial_type','medical')
            ->whereRaw("financial_data->>'medication_type' = 'medicine'")
            ->selectRaw("financial_data->>'disease_type' as disease, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('disease')->orderByDesc('total')->limit(10)->get();

        $medicineCostBreakdown = $this->getDynamicNumericBreakdown('medical', 'medicine', ['disease_type','invoice_number']);

        // ── Hospital: operation grouping + dynamic numeric JSONB fields ───────
        $hospitalByOperation = ActivityFinancial::where('financial_type','medical')
            ->whereRaw("financial_data->>'medication_type' = 'hospital'")
            ->selectRaw("financial_data->>'operation_type' as op, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('op')->orderByDesc('total')->limit(10)->get();

        $hospitalCostBreakdown = $this->getDynamicNumericBreakdown('medical', 'hospital', ['operation_type']);

        // ── KPIs ────────────────────────────────────────────────────────────
        $kpis = [
            'grand_total'   => (float) ActivityFinancial::sum('amount'),
            'total_records' => ActivityFinancial::count(),
            'paid_total'    => (float) ActivityFinancial::where('payment_status','paid')->sum('amount'),
            'pending_total' => (float) ActivityFinancial::where('payment_status','pending')->sum('amount'),
            'overdue_total' => (float) ActivityFinancial::where('payment_status','overdue')->sum('amount'),
            'omt_total'     => (float) ($byType->get('omt')->total ?? 0),
            'medical_total' => (float) ($byType->get('medical')->total ?? 0),
        ];

        return view('financials.visualization', compact(
            'page','kpis','byType','byStatus','monthly',
            'omtBreakdown','omtByActivity',
            'medicineByDisease','medicineCostBreakdown',
            'hospitalByOperation','hospitalCostBreakdown'
        ));
    }

    private function getDynamicNumericBreakdown(string $financialType, ?string $medicationType, array $excludeKeys = ['medication_type']): array
    {
        try {
            $bindings = [$financialType];
            $medFilter = '';
            if ($medicationType !== null) {
                $medFilter = " AND financial_data->>'medication_type' = ?";
                $bindings[] = $medicationType;
            }

            $keys = DB::select(
                "SELECT DISTINCT jsonb_object_keys(financial_data) as key
                 FROM activity_financials
                 WHERE financial_type = ?" . $medFilter,
                $bindings
            );

            $breakdown = [];
            foreach ($keys as $row) {
                $key = preg_replace('/[^a-z0-9_]/i', '', $row->key);
                if (!$key || in_array($key, $excludeKeys)) continue;

                $result = DB::select(
                    "SELECT COALESCE(SUM(
                        CASE WHEN (financial_data->>'$key') ~ '^-?[0-9]+(\.[0-9]+)?$'
                        THEN (financial_data->>'$key')::numeric
                        ELSE 0 END
                    ), 0) as val
                    FROM activity_financials
                    WHERE financial_type = ?" . $medFilter,
                    $bindings
                );

                $val = (float) ($result[0]->val ?? 0);
                if ($val > 0) $breakdown[$key] = $val;
            }

            return $breakdown;
        } catch (\Exception $e) {
            Log::error('getDynamicNumericBreakdown error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Display Medicine financial records
     */
    public function medicineIndex(Request $request)
    {
        $query = ActivityFinancial::with(['activity', 'user', 'cop'])
            ->where('financial_type', 'medical')
            ->whereRaw("financial_data->>'medication_type' = ?", ['medicine'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tx_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tx_date', '<=', $request->end_date);
        }

        if ($request->filled('disease_type')) {
            $query->whereRaw("financial_data->>'disease_type' = ?", [$request->disease_type]);
        }

        if ($request->filled('activity_search')) {
            $search = $request->activity_search;
            $query->whereHas('activity', function ($q) use ($search) {
                $q->where('activity_title_en', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $financials = $query->paginate($perPage);

        // Get distinct disease types for filter - FIXED JSONB SYNTAX
        $diseaseTypes = ActivityFinancial::where('financial_type', 'medical')
            ->whereRaw("financial_data->>'medication_type' = ?", ['medicine'])
            ->whereRaw("financial_data->>'disease_type' IS NOT NULL")
            ->selectRaw("DISTINCT financial_data->>'disease_type' as disease_type")
            ->pluck('disease_type')
            ->filter()
            ->values();

        $activities = Activity::orderBy('activity_title_en')->limit(100)->get();
        $users = User::orderBy('first_name')->limit(100)->get();

        return view('financials.medical.medicine', compact('financials', 'activities', 'users', 'diseaseTypes'));
    }

    /**
     * Display Hospital financial records
     */
    public function hospitalIndex(Request $request)
    {
        $query = ActivityFinancial::with(['activity', 'user', 'cop'])
            ->where('financial_type', 'medical')
            ->whereRaw("financial_data->>'medication_type' = ?", ['hospital'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tx_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tx_date', '<=', $request->end_date);
        }

        if ($request->filled('operation_type')) {
            $query->whereRaw("financial_data->>'operation_type' = ?", [$request->operation_type]);
        }

        if ($request->filled('activity_search')) {
            $search = $request->activity_search;
            $query->whereHas('activity', function ($q) use ($search) {
                $q->where('activity_title_en', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $financials = $query->paginate($perPage);

        // Get distinct operation types for filter - FIXED JSONB SYNTAX
        $operationTypes = ActivityFinancial::where('financial_type', 'medical')
            ->whereRaw("financial_data->>'medication_type' = ?", ['hospital'])
            ->whereRaw("financial_data->>'operation_type' IS NOT NULL")
            ->selectRaw("DISTINCT financial_data->>'operation_type' as operation_type")
            ->pluck('operation_type')
            ->filter()
            ->values();

        $activities = Activity::orderBy('activity_title_en')->limit(100)->get();
        $users = User::orderBy('first_name')->limit(100)->get();

        return view('financials.medical.hospital', compact('financials', 'activities', 'users', 'operationTypes'));
    }
}