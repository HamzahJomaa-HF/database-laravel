<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityFinancial;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinancialController extends Controller
{
    /**
     * Display a listing of financial records.
     * GET /api/financials
     */
    public function index(Request $request)
    {
        $query = ActivityFinancial::with(['activity', 'user']);

        // Filter by financial type
        if ($request->has('financial_type') && $request->financial_type) {
            $query->where('financial_type', $request->financial_type);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by activity
        if ($request->has('activity_id') && $request->activity_id) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('tx_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('tx_date', '<=', $request->to_date);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%");
            });
        }

        $financials = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $financials,
            'message' => 'Financial records retrieved successfully'
        ]);
    }

    /**
     * Store a newly created financial record.
     * POST /api/financials
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules($request));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Build financial_data based on type
            $financialData = $this->buildFinancialData($request);

            $financial = ActivityFinancial::create([
                'activity_id' => $request->activity_id,
                'user_id' => $request->user_id,
                'cop_id' => $request->cop_id,
                'financial_type' => $request->financial_type,
                'amount' => $request->amount,
                'payment_status' => $request->payment_status,
                'tx_date' => $request->tx_date,
                'financial_data' => $financialData,
                'notes' => $request->notes,
            ]);

            DB::commit();

            // Load relationships
            $financial->load(['activity', 'user']);

            return response()->json([
                'success' => true,
                'data' => $financial,
                'message' => 'Financial record created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating financial record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified financial record.
     * GET /api/financials/{id}
     */
    public function show($id)
    {
        $financial = ActivityFinancial::with(['activity', 'user', 'cop'])
            ->find($id);

        if (!$financial) {
            return response()->json([
                'success' => false,
                'message' => 'Financial record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $financial,
            'message' => 'Financial record retrieved successfully'
        ]);
    }

    /**
     * Update the specified financial record.
     * PUT /api/financials/{id}
     */
    public function update(Request $request, $id)
    {
        $financial = ActivityFinancial::find($id);

        if (!$financial) {
            return response()->json([
                'success' => false,
                'message' => 'Financial record not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules($request, true));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Build financial_data based on type
            $financialData = $this->buildFinancialData($request);

            $financial->update([
                'activity_id' => $request->activity_id ?? $financial->activity_id,
                'user_id' => $request->user_id ?? $financial->user_id,
                'cop_id' => $request->cop_id ?? $financial->cop_id,
                'financial_type' => $request->financial_type ?? $financial->financial_type,
                'amount' => $request->amount ?? $financial->amount,
                'payment_status' => $request->payment_status ?? $financial->payment_status,
                'tx_date' => $request->tx_date ?? $financial->tx_date,
                'financial_data' => $financialData,
                'notes' => $request->notes ?? $financial->notes,
            ]);

            DB::commit();

            $financial->load(['activity', 'user']);

            return response()->json([
                'success' => true,
                'data' => $financial,
                'message' => 'Financial record updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating financial record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified financial record.
     * DELETE /api/financials/{id}
     */
    public function destroy($id)
    {
        $financial = ActivityFinancial::find($id);

        if (!$financial) {
            return response()->json([
                'success' => false,
                'message' => 'Financial record not found'
            ], 404);
        }

        try {
            $financial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Financial record deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting financial record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics.
     * GET /api/financials/summary
     */
    public function summary(Request $request)
    {
        $query = ActivityFinancial::query();

        if ($request->has('financial_type') && $request->financial_type) {
            $query->where('financial_type', $request->financial_type);
        }

        $summary = [
            'total_budget' => $query->sum('amount'),
            'total_records' => $query->count(),
            'by_type' => [
                'omt' => ActivityFinancial::omt()->sum('amount'),
                'medical' => ActivityFinancial::medical()->sum('amount'),
                'education' => ActivityFinancial::education()->sum('amount'),
            ],
            'by_status' => [
                'paid' => ActivityFinancial::where('payment_status', 'paid')->sum('amount'),
                'pending' => ActivityFinancial::where('payment_status', 'pending')->sum('amount'),
                'partial' => ActivityFinancial::where('payment_status', 'partial')->sum('amount'),
                'overdue' => ActivityFinancial::where('payment_status', 'overdue')->sum('amount'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
            'message' => 'Summary retrieved successfully'
        ]);
    }

    /**
     * Get validation rules based on financial type.
     */
    private function getValidationRules($request, $isUpdate = false)
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        $rules = [
            'activity_id' => $required . '|uuid|exists:activities,activity_id',
            'user_id' => $required . '|uuid|exists:users,user_id',
            'cop_id' => 'nullable|uuid|exists:cops,cop_id',
            'financial_type' => $required . '|in:omt,medical,education',
            'amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid,overdue',
            'tx_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

        // Type-specific validation rules
        if ($request->financial_type === 'omt' || (!$isUpdate && $request->financial_type === 'omt')) {
            $rules['operational_cost'] = 'nullable|numeric|min:0';
            $rules['personnel_cost'] = 'nullable|numeric|min:0';
            $rules['travel_cost'] = 'nullable|numeric|min:0';
            $rules['equipment_cost'] = 'nullable|numeric|min:0';
            $rules['supplies_cost'] = 'nullable|numeric|min:0';
        }

        if ($request->financial_type === 'medical' || (!$isUpdate && $request->financial_type === 'medical')) {
            $rules['medical_category'] = 'required|in:medication,hospital,both';
            $rules['medication_cost'] = 'nullable|numeric|min:0';
            $rules['hospital_cost'] = 'nullable|numeric|min:0';
            $rules['doctor_fees'] = 'nullable|numeric|min:0';
            $rules['patient_count'] = 'nullable|integer|min:1';
        }

        if ($request->financial_type === 'education' || (!$isUpdate && $request->financial_type === 'education')) {
            $rules['scholarship_percentage'] = 'required|numeric|min:0|max:100';
            $rules['tuition_fees'] = 'nullable|numeric|min:0';
            $rules['student_count'] = 'required|integer|min:1';
            $rules['education_level'] = 'required|in:high_school,bachelor,master,phd,diploma';
            $rules['institution_name'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Build financial_data array based on type.
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
            ];
        }

        if ($request->financial_type === 'medical') {
            $data = [
                'medical_category' => $request->medical_category,
                'medication_cost' => $request->medication_cost,
                'hospital_cost' => $request->hospital_cost,
                'doctor_fees' => $request->doctor_fees,
                'lab_tests' => $request->lab_tests,
                'insurance_coverage' => $request->insurance_coverage,
                'patient_count' => $request->patient_count,
                'follow_up_cost' => $request->follow_up_cost,
                'ambulance_cost' => $request->ambulance_cost,
            ];
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

        // Remove null values
        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }
}