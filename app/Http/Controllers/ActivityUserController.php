<?php
namespace App\Http\Controllers;

use App\Models\ActivityUser;
use App\Models\Activity;
use App\Models\User;
use App\Models\Cop;
use App\Models\Nationality;
use App\Models\Diploma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ActivityUserController extends Controller
{
    /**
     * Display a listing of activity-user relationships.
     */
    public function index(Request $request)
    {
        // ... (keep your existing index method exactly as is)
        $query = ActivityUser::with(['user', 'activity', 'cop'])
            ->orderBy('created_at', 'desc');

        // Filter by activity if provided
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        if($request->filled('venue')) {
            $venue = $request->venue;
            $query->whereHas('activity', function ($activityQuery) use ($venue) {
                $activityQuery->where('venue', $venue);
            });
        }

        // Filter by user if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by cop if provided
        if ($request->filled('cop_id')) {
            $query->where('cop_id', $request->cop_id);
        }

        if ($request->filled('type')) {
            $type = $request->type;
            $query->where('type', $type);
        }

        // Filter by invited status
        if ($request->has('invited') && $request->invited !== '') {
            $query->where('invited', $request->boolean('invited'));
        }

        // Filter by attended status
        if ($request->has('attended') && $request->attended !== '') {
            $query->where('attended', $request->boolean('attended'));
        }
        
        // Filter by activity start date
        if ($request->filled('start_date')) {
            $startDate = $request->start_date;
            $query->whereHas('activity', function ($activityQuery) use ($startDate) {
                $activityQuery->whereDate('start_date', $startDate);
            });
        }

        // Global search by user or activity names
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'ilike', "%{$search}%")
                              ->orWhere('middle_name', 'ilike', "%{$search}%")
                              ->orWhere('last_name', 'ilike', "%{$search}%")
                              ->orWhere('email', 'ilike', "%{$search}%")
                              ->orWhere('phone_number', 'ilike', "%{$search}%")
                              ->orWhere('identification_id', 'ilike', "%{$search}%")
                              ->orWhere('passport_number', 'ilike', "%{$search}%")
                              ->orWhere('register_number', 'ilike', "%{$search}%")
                              ->orWhere('person_id', 'ilike', "%{$search}%")
                              ->orWhere('istimara_id', 'ilike', "%{$search}%");
                })
                ->orWhereHas('activity', function ($activityQuery) use ($search) {
                    $activityQuery->where('activity_title_en', 'ilike', "%{$search}%")
                                  ->orWhere('activity_title_ar', 'ilike', "%{$search}%");
                });
            });
        }
        
        // User-specific search
        if ($request->filled('user_search')) {
            $userSearch = $request->user_search;
            
            $query->whereHas('user', function ($userQuery) use ($userSearch) {
                $userQuery->where(function($q) use ($userSearch) {
                    $q->where('first_name', 'ilike', "%{$userSearch}%")
                      ->orWhere('middle_name', 'ilike', "%{$userSearch}%")
                      ->orWhere('last_name', 'ilike', "%{$userSearch}%")
                      ->orWhere('email', 'ilike', "%{$userSearch}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ilike', "%{$userSearch}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'ilike', "%{$userSearch}%")
                      ->orWhere('person_id', 'ilike', "%{$userSearch}%")
                      ->orWhere('istimara_id', 'ilike', "%{$userSearch}%");
                });
            });
        }
        
        // Activity-specific search
        if ($request->filled('activity_search')) {
            $activitySearch = $request->activity_search;
            $query->whereHas('activity', function ($activityQuery) use ($activitySearch) {
                $activityQuery->where(function($q) use ($activitySearch) {
                    $q->where('activity_title_en', 'ilike', "%{$activitySearch}%")
                      ->orWhere('activity_title_ar', 'ilike', "%{$activitySearch}%")
                      ->orWhere('activity_type', 'ilike', "%{$activitySearch}%")
                      ->orWhere('venue', 'ilike', "%{$activitySearch}%");
                });
            });
        }

        // Phone number search
        if ($request->filled('phone_search')) {
            $phoneSearch = $request->phone_search;
            $query->whereHas('user', function ($userQuery) use ($phoneSearch) {
                $userQuery->where('phone_number', 'ilike', "%{$phoneSearch}%");
            });
        }

        // Handle pagination
        $perPage = $request->get('per_page', 20);
        $activityUsers = $query->paginate($perPage);

        // Get data for filter dropdowns
        $activities = Activity::orderBy('activity_title_en')
            ->limit(100)
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        
        $users = User::orderBy('first_name')
            ->limit(100)
            ->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email', 'person_id', 'istimara_id']);
        
        $cops = Cop::orderBy('cop_name')
            ->limit(100)
            ->get(['cop_id', 'cop_name']);

        // Get distinct types for filter dropdown
        $types = ActivityUser::distinct()->whereNotNull('type')->pluck('type')->filter()->values();

        // Check if request expects JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $activityUsers,
                'filters' => [
                    'activities' => $activities,
                    'users' => $users,
                    'cops' => $cops,
                    'types' => $types
                ]
            ]);
        }

        return view('activity-users.index', compact('activityUsers', 'activities', 'users', 'cops', 'types'));
    }
    
    /**
     * Show the form for creating a new activity-user relationship.
     */
    public function create()
    {
        // ... (keep your existing create method)
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->limit(100)->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email', 'person_id', 'istimara_id']);
        
        // Get available types for the dropdown
        $availableTypes = ['Stakeholder', 'Beneficiary'];

        return view('activity-users.create', compact('cops', 'activities', 'users', 'availableTypes'));
    }

    /**
     * Store a newly created activity-user relationship in storage.
     */
    public function store(Request $request)
    {
        // ... (keep your existing store method)
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_id' => 'required|exists:activities,activity_id',
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255|in:Stakeholder,Beneficiary',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
            'external_id' => 'nullable|string|max:255|unique:activity_users,external_id',
        ]);

        // Set default values for checkboxes if not present
        $validated['invited'] = $request->boolean('invited', false);
        $validated['attended'] = $request->boolean('attended', false);

        // Generate UUID for activity_user_id if not using database default
        if (!isset($validated['activity_user_id'])) {
            $validated['activity_user_id'] = (string) Str::uuid();
        }

        // Check if relationship already exists
        $exists = ActivityUser::where('user_id', $validated['user_id'])
            ->where('activity_id', $validated['activity_id'])
            ->exists();

        if ($exists) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already assigned to this activity.'
                ], 422);
            }
            return back()->withErrors(['user_id' => 'This user is already assigned to this activity.'])->withInput();
        }

        try {
            DB::beginTransaction();
            
            $activityUser = ActivityUser::create($validated);
            
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity-User relationship created successfully!',
                    'data' => $activityUser->load(['user', 'activity', 'cop'])
                ], 201);
            }

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create activity-user relationship: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create relationship: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to create relationship: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified activity-user relationship.
     */
    public function edit($id)
    {
        // ... (keep your existing edit method)
        $activityUser = ActivityUser::findOrFail($id);
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email', 'person_id', 'istimara_id']);
        
        // Get available types for the dropdown
        $availableTypes = ['Stakeholder', 'Beneficiary'];

        return view('activity-users.edit', compact('activityUser', 'cops', 'activities', 'users', 'id', 'availableTypes'));
    }

    /**
     * Update the specified activity-user relationship in storage.
     */
    public function update(Request $request, $id)
    {
        // ... (keep your existing update method)
        $activityUser = ActivityUser::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_id' => 'required|exists:activities,activity_id',
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255|in:Stakeholder,Beneficiary',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
        ]);

        // Set default values for checkboxes if not present
        $validated['invited'] = $request->boolean('invited', false);
        $validated['attended'] = $request->boolean('attended', false);

        try {
            DB::beginTransaction();
            
            $activityUser->update($validated);
            
            DB::commit();

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update activity-user relationship: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update relationship: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified activity-user relationship from storage.
     */
    public function destroy($id)
    {
        // ... (keep your existing destroy method)
        $activityUser = ActivityUser::findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->delete();
            
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity-User relationship deleted successfully!'
                ]);
            }

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete activity-user relationship: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete relationship: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete relationship: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted activity-user relationship.
     */
    public function restore($id)
    {
        // ... (keep your existing restore method)
        $activityUser = ActivityUser::withTrashed()->findOrFail($id);

        try {
            $activityUser->restore();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity-User relationship restored successfully!'
                ]);
            }

            return redirect()->route('activity-users.trash')
                ->with('success', 'Activity-User relationship restored successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to restore activity-user relationship: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore relationship: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to restore relationship: ' . $e->getMessage()]);
        }
    }

    /**
     * Force delete a soft-deleted activity-user relationship.
     */
    public function forceDelete($id)
    {
        // ... (keep your existing forceDelete method)
        $activityUser = ActivityUser::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->forceDelete();
            
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity-User relationship permanently deleted!'
                ]);
            }

            return redirect()->route('activity-users.trash')
                ->with('success', 'Activity-User relationship permanently deleted!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to permanently delete activity-user relationship: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to permanently delete relationship: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to permanently delete relationship: ' . $e->getMessage()]);
        }
    }

    /**
     * Export activity users to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityUser::with(['user', 'activity', 'cop'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('cop_id')) {
            $query->where('cop_id', $request->cop_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('invited') && $request->invited !== '') {
            $query->where('invited', $request->boolean('invited'));
        }

        if ($request->has('attended') && $request->attended !== '') {
            $query->where('attended', $request->boolean('attended'));
        }

        if ($request->filled('venue')) {
            $venue = $request->venue;
            $query->whereHas('activity', function ($q) use ($venue) {
                $q->where('venue', $venue);
            });
        }

        if ($request->filled('start_date')) {
            $startDate = $request->start_date;
            $query->whereHas('activity', function ($q) use ($startDate) {
                $q->whereDate('start_date', $startDate);
            });
        }

        if ($request->filled('user_search')) {
            $userSearch = $request->user_search;
            $query->whereHas('user', function ($q) use ($userSearch) {
                $q->where(function ($inner) use ($userSearch) {
                    $inner->where('first_name', 'ilike', "%{$userSearch}%")
                          ->orWhere('middle_name', 'ilike', "%{$userSearch}%")
                          ->orWhere('last_name', 'ilike', "%{$userSearch}%")
                          ->orWhere('email', 'ilike', "%{$userSearch}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ilike', "%{$userSearch}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'ilike', "%{$userSearch}%")
                          ->orWhere('person_id', 'ilike', "%{$userSearch}%")
                          ->orWhere('istimara_id', 'ilike', "%{$userSearch}%");
                });
            });
        }

        if ($request->filled('activity_search')) {
            $activitySearch = $request->activity_search;
            $query->whereHas('activity', function ($q) use ($activitySearch) {
                $q->where(function ($inner) use ($activitySearch) {
                    $inner->where('activity_title_en', 'ilike', "%{$activitySearch}%")
                          ->orWhere('activity_title_ar', 'ilike', "%{$activitySearch}%")
                          ->orWhere('activity_type', 'ilike', "%{$activitySearch}%")
                          ->orWhere('venue', 'ilike', "%{$activitySearch}%");
                });
            });
        }

        if ($request->filled('phone_search')) {
            $phoneSearch = $request->phone_search;
            $query->whereHas('user', function ($q) use ($phoneSearch) {
                $q->where('phone_number', 'ilike', "%{$phoneSearch}%");
            });
        }

        $activityUsers = $query->get();

        $filename = 'activity-users-' . now()->format('Y-m-d-His') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add headers
        fputcsv($handle, [
            'ID',
            'User Name',
            'User Email',
            'User Phone',
            'User Person ID',
            'User Istimara ID',
            'User Type',
            'Activity Title (EN)',
            'Activity Title (AR)',
            'Activity Type',
            'Activity Date',
            'Role/Type',
            'Invited',
            'Attended',
            'COP',
            'External ID',
            'Created At'
        ]);

        // Add data rows
        foreach ($activityUsers as $item) {
            $userName = '';
            if ($item->user) {
                $userName = $item->user->first_name;
                if ($item->user->middle_name) {
                    $userName .= ' ' . $item->user->middle_name;
                }
                $userName .= ' ' . $item->user->last_name;
            }

            fputcsv($handle, [
                $item->activity_user_id,
                $userName,
                $item->user ? $item->user->email : '',
                $item->user ? $item->user->phone_number : '',
                $item->user ? $item->user->person_id : '',
                $item->user ? $item->user->istimara_id : '',
                $item->type,
                $item->activity ? $item->activity->activity_title_en : '',
                $item->activity ? $item->activity->activity_title_ar : '',
                $item->activity ? $item->activity->activity_type : '',
                $item->activity ? $item->activity->start_date : '',
                $item->type,
                $item->invited ? 'Yes' : 'No',
                $item->attended ? 'Yes' : 'No',
                $item->cop ? $item->cop->cop_name : '',
                $item->external_id,
                $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        fclose($handle);
        exit;
    }

    /**
     * Remove multiple activity-user relationships from storage.
     */
    public function bulkDestroy(Request $request)
    {
        // ... (keep your existing bulkDestroy method)
        $request->validate([
            'activity_user_ids' => 'required|array',
            'activity_user_ids.*' => 'uuid|exists:activity_users,activity_user_id'
        ]);

        try {
            DB::beginTransaction();

            $count = ActivityUser::whereIn('activity_user_id', $request->activity_user_ids)->count();
            ActivityUser::whereIn('activity_user_id', $request->activity_user_ids)->delete();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$count} activity-user relationship(s) deleted successfully!"
                ]);
            }

            return redirect()->route('activity-users.index')
                ->with('success', "{$count} activity-user relationship(s) deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to bulk delete activity-user relationships: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete relationships: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete relationships: ' . $e->getMessage()]);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        // ... (keep your existing downloadTemplate method)
        $filename = 'activity-users-import-template-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM for UTF-8
            
            // Template headers
            fputcsv($file, [
                'first_name*',
                'last_name*',
                'gender',
                'position_1',
                'organization_1',
                'organization_type_1',
                'status_1',
                'address',
                'phone_number',
                'scope',
                'is_high_profile',
                'email',
                'middle_name',
                'mother_name',
                'dob',
                'office_phone',
                'home_phone',
                'position_2',
                'organization_2',
                'organization_type_2',
                'status_2',
                'sector',
                'identification_id',
                'passport_number',
                'register_number',
                'register_place',
                'marital_status',
                'employment_status',
                'type',
                'default_cop_id',
                'role_in_activity',
                'attended',
                'nationality_name',
                'diploma_name',
                'diploma_institution',
                'diploma_year',
                'person_id',
                'istimara_id'
            ]);
            
            // Example data row
            fputcsv($file, [
                'John',                         // first_name*
                'Doe',                          // last_name*
                'Male',                         // gender
                'Senior Manager',               // position_1
                'Beirut Medical Center',        // organization_1
                'Public Sector',                // organization_type_1
                'Active',                       // status_1
                'Beirut, Lebanon',              // address
                '+961 70 123 456',              // phone_number
                'National',                     // scope
                'true',                         // is_high_profile
                'john.doe@example.com',         // email
                'Michael',                      // middle_name
                'Jane Doe',                     // mother_name
                '1980-01-15',                   // dob
                '+961 1 123456',                // office_phone
                '+961 3 789012',                // home_phone
                'Consultant',                   // position_2
                'Ministry of Health',           // organization_2
                'Public Sector',                // organization_type_2
                'Part-time',                    // status_2
                'Healthcare',                   // sector
                'ID123456',                     // identification_id
                'PASS123456',                   // passport_number
                'REG789012',                    // register_number
                'Beirut',                       // register_place
                'Married',                      // marital_status
                'Employed',                     // employment_status
                'Stakeholder',                  // type (this will go to activity_users.type)
                '123e4567-e89b-12d3-a456-426614174000', // default_cop_id
                'Participant',                  // role_in_activity
                'yes',                          // attended
                'Lebanese',                     // nationality_name
                'Bachelor of Science',          // diploma_name
                'American University of Beirut', // diploma_institution
                '2020',                         // diploma_year
                'PERSON123456',                 // person_id
                'ISTIMARA789012'                // istimara_id
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * ENHANCED: Process the import file with chunking for large files
     */
    public function import(Request $request)
    {
        // Increase memory and execution time for large files
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '600'); // 10 minutes
        set_time_limit(600);
        
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:102400', // 100MB max
            'cop_id' => 'nullable|exists:cops,cop_id',
            'invited_default' => 'nullable|boolean',
            'attended_default' => 'nullable|boolean',
            'default_user_type' => 'nullable|in:Stakeholder,Beneficiary'
        ]);
        
        $activityId = $request->activity_id;
        $copId = $request->cop_id;
        $invitedDefault = $request->boolean('invited_default', false);
        $attendedDefault = $request->boolean('attended_default', false);
        $defaultUserType = $request->default_user_type;
        
        $results = [
            'total'            => 0,
            'new_users'        => 0,
            'existing_users'   => 0,
            'already_assigned' => 0,
            'assigned'         => 0,
            'failed'           => 0,
            'duplicates'       => 0,
            'merged_users'     => 0,
            'errors'           => [],
            'not_entered_names' => [],  // collects every name that did not enter the DB
        ];
        
        try {
            $file = $request->file('import_file');
            $extension = $file->getClientOriginalExtension();
            
            // For Excel files, convert to CSV first to save memory
            if ($extension !== 'csv') {
                $csvFile = $this->convertExcelToCsv($file);
                $handle = fopen($csvFile, 'r');
                $tempFile = $csvFile;
            } else {
                $handle = fopen($file->getPathname(), 'r');
                $tempFile = null;
            }
            
            if (!$handle) {
                throw new \Exception('Cannot open file');
            }
            
            // Get headers
            $headers = fgetcsv($handle);
            if (!$headers) {
                throw new \Exception('Invalid file format');
            }
            
            // Clean headers: strip UTF-8 BOM (added by Excel/CSV tools to first column), *, and whitespace
            $headers = array_map(function($header) {
                return trim(str_replace(["\xEF\xBB\xBF", '*'], '', $header));
            }, $headers);
            
            Log::info('Import Headers', ['headers' => $headers]);
            
            // ── Import start diagnostic ─────────────────────────────────────────
            $importFile = $request->file('import_file');
            Log::info('[IMPORT-START]', [
                'activity_id'        => $activityId,
                'file_name'          => $importFile->getClientOriginalName(),
                'file_size_bytes'    => $importFile->getSize(),
                'file_extension'     => $extension,
                'php_memory_limit'   => ini_get('memory_limit'),
                'php_upload_max'     => ini_get('upload_max_filesize'),
                'php_post_max'       => ini_get('post_max_size'),
                'php_max_exec'       => ini_get('max_execution_time'),
                'headers_found'      => $headers,
            ]);

            // Process in chunks to manage memory
            $chunkSize = 500;
            $chunkData = [];
            $rowNumber = 1; // Header is row 1
            $chunkNumber = 0;
            $emptyRowsSkipped = 0;

            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                $results['total']++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    $emptyRowsSkipped++;
                    Log::debug('[IMPORT-SKIP-EMPTY] Row ' . $rowNumber . ' is blank — skipped');
                    continue;
                }

                // Pad row if needed
                if (count($row) < count($headers)) {
                    Log::warning('[IMPORT-ROW-SHORT] Row ' . $rowNumber . ' has ' . count($row) . ' columns, expected ' . count($headers) . ' — padded with empty strings');
                    $row = array_pad($row, count($headers), '');
                }

                // Map row to associative array
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $row[$index] ?? '';
                }

                $chunkData[] = $rowData;

                // Process chunk when it reaches the limit
                if (count($chunkData) >= $chunkSize) {
                    $chunkNumber++;
                    $chunkStart = $rowNumber - count($chunkData);
                    Log::info('[IMPORT-CHUNK-START] Chunk #' . $chunkNumber . ' | rows ' . $chunkStart . '–' . ($chunkStart + count($chunkData) - 1) . ' | running totals so far: assigned=' . $results['assigned'] . ' already_assigned=' . $results['already_assigned'] . ' failed=' . $results['failed']);
                    $this->processImportChunk($chunkData, $activityId, $copId, $invitedDefault, $attendedDefault, $defaultUserType, $results, $chunkStart);
                    Log::info('[IMPORT-CHUNK-END]   Chunk #' . $chunkNumber . ' complete | assigned=' . $results['assigned'] . ' already_assigned=' . $results['already_assigned'] . ' failed=' . $results['failed'] . ' new_users=' . $results['new_users'] . ' existing=' . $results['existing_users']);
                    $chunkData = [];
                    gc_collect_cycles();
                }
            }

            // Process remaining rows
            if (!empty($chunkData)) {
                $chunkNumber++;
                $chunkStart = $rowNumber - count($chunkData);
                Log::info('[IMPORT-CHUNK-START] Chunk #' . $chunkNumber . ' (final) | rows ' . $chunkStart . '–' . ($chunkStart + count($chunkData) - 1));
                $this->processImportChunk($chunkData, $activityId, $copId, $invitedDefault, $attendedDefault, $defaultUserType, $results, $chunkStart);
                Log::info('[IMPORT-CHUNK-END]   Chunk #' . $chunkNumber . ' (final) complete');
            }

            fclose($handle);

            // Clean up temp file if created
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }

            DB::commit();

            // ── Import end summary ───────────────────────────────────────────────
            $totalNotEntered = $results['already_assigned'] + $results['failed'];
            Log::info('[IMPORT-SUMMARY]', [
                'activity_id'       => $activityId,
                'file_name'         => $importFile->getClientOriginalName(),
                'total_rows_read'   => $results['total'],
                'empty_skipped'     => $emptyRowsSkipped,
                'assigned_new'      => $results['assigned'],
                'already_assigned'  => $results['already_assigned'],
                'failed'            => $results['failed'],
                'total_not_entered' => $totalNotEntered,
                'new_users_created' => $results['new_users'],
                'existing_users'    => $results['existing_users'],
                'merged_users'      => $results['merged_users'],
                'first_20_errors'   => array_slice($results['errors'], 0, 20),
            ]);

            // ── End-of-import: full list of names that did NOT enter the DB ──────
            if (!empty($results['not_entered_names'])) {
                $lines   = [];
                $lines[] = '';
                $lines[] = '╔══════════════════════════════════════════════════════════════╗';
                $lines[] = '  NAMES THAT DID NOT ENTER THE DATABASE';
                $lines[] = '  File     : ' . $importFile->getClientOriginalName();
                $lines[] = '  Activity : ' . $activityId;
                $lines[] = '  Date     : ' . now()->format('Y-m-d H:i:s');
                $lines[] = '  Total    : ' . $totalNotEntered . ' name(s) blocked';
                $lines[] = '╚══════════════════════════════════════════════════════════════╝';
                $lines[] = '';

                foreach ($results['not_entered_names'] as $i => $entry) {
                    $num     = str_pad($i + 1, 5, ' ', STR_PAD_LEFT);
                    $name    = str_pad($entry['name'],      35);
                    $pid     = 'person_id: ' . str_pad($entry['person_id'], 12);
                    $phone   = 'phone: '     . str_pad($entry['phone'],     18);
                    $reason  = 'reason: '    . $entry['reason'];
                    $lines[] = $num . '.  ' . $name . ' | ' . $pid . ' | ' . $phone . ' | ' . $reason;
                }

                $lines[] = '';
                $lines[] = '══════════════════════════════════════════════════════════════';
                $lines[] = '  END OF NOT-ENTERED LIST  (' . $totalNotEntered . ' total)';
                $lines[] = '══════════════════════════════════════════════════════════════';
                $lines[] = '';

                Log::warning('[IMPORT-NOT-ENTERED-LIST]' . PHP_EOL . implode(PHP_EOL, $lines));
            } else {
                Log::info('[IMPORT-NOT-ENTERED-LIST] All ' . $results['total'] . ' rows were successfully assigned — no blocked names.');
            }

            // Build response message
            return $this->buildImportResponse($results);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[IMPORT-FATAL] Import crashed: ' . $e->getMessage(), [
                'activity_id' => $activityId ?? null,
                'trace'       => $e->getTraceAsString(),
            ]);
            
            // Clean up temp file if exists
            if (isset($tempFile) && $tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            return redirect()
                ->route('activity-users.import.form')
                ->with('error', '❌ Failed to process file: ' . $e->getMessage());
        }
    }
    
    /**
     * Process a chunk of import data
     */
    private function processImportChunk($chunkData, $activityId, $copId, $invitedDefault, $attendedDefault, $defaultUserType, &$results, $startRow)
    {
        $importedUsersCache = [];

        foreach ($chunkData as $index => $row) {
            $rowNumber = $startRow + $index + 1;

            // Create a savepoint for each row to handle individual failures
            $savepoint = "row_" . uniqid();
            DB::statement("SAVEPOINT {$savepoint}");

            try {
                // Validate required fields
                $requiredFields = ['first_name', 'last_name'];
                $missingFields = [];
                foreach ($requiredFields as $field) {
                    if (empty($row[$field]) && $row[$field] !== '0') {
                        $missingFields[] = $field;
                    }
                }

                if (!empty($missingFields)) {
                    throw new \Exception("Missing required fields: " . implode(', ', $missingFields));
                }

                // Validate name length
                $firstName = trim($row['first_name']);
                $lastName  = trim($row['last_name']);

                if (strlen($firstName) < 2 || strlen($lastName) < 2) {
                    throw new \Exception("Name too short — first_name='{$firstName}' last_name='{$lastName}' (min 2 chars each)");
                }

                // Parse attended value
                $attendedValue = null;
                if (isset($row['attended']) && !empty($row['attended'])) {
                    $attendedValue = $this->parseBoolean($row['attended']);
                }

                // Parse type value
                $activityUserType = null;
                if (!empty($row['type'])) {
                    $activityUserType = ucfirst(strtolower(trim($row['type'])));
                    $allowedTypes = ['Stakeholder', 'Beneficiary'];
                    if (!in_array($activityUserType, $allowedTypes)) {
                        Log::warning('[IMPORT-ROW-TYPE] Row ' . $rowNumber . " — invalid type '{$activityUserType}', falling back to default '{$defaultUserType}'");
                        $activityUserType = $defaultUserType;
                    }
                } elseif ($defaultUserType) {
                    $activityUserType = $defaultUserType;
                }

                // Prepare user data
                $userData = $this->prepareUserData($row);

                // Find or create user — matchInfo explains HOW the user was resolved
                $matchInfo = [];
                $user = $this->findOrCreateUserOptimized($userData, $results, $matchInfo);

                if (!$user) {
                    throw new \Exception("findOrCreateUserOptimized returned null unexpectedly");
                }

                // Name-mismatch detection — must happen before the relationship check
                // so we can recover from corrupted person_id data written by old buggy imports.
                $csvName      = trim($firstName . ' ' . $lastName);
                $dbName       = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $nameMismatch = (strtolower($csvName) !== strtolower($dbName));

                // Corruption recovery: if we matched by person_id but the names differ,
                // a previous import wrote person_id=X onto the wrong user (via name/istimara
                // fallback). Strip that person_id from the wrong user and create a fresh one.
                if ($nameMismatch
                    && isset($matchInfo['matched_by'])
                    && str_starts_with($matchInfo['matched_by'], 'person_id:')
                    && $matchInfo['method'] !== 'cache'
                ) {
                    Log::error(
                        '[IMPORT-CORRUPTION-RECOVERY] Row ' . $rowNumber
                        . ' | CSV: "' . $csvName . '" person_id=' . ($userData['person_id'] ?? 'N/A')
                        . ' | DB user_id=' . $user->user_id . ' has name "' . $dbName . '"'
                        . ' | Stripping wrong person_id from DB user and creating correct new user'
                    );

                    // Remove the wrongly-attached person_id so it no longer blocks this person
                    $user->person_id = null;
                    $user->save();

                    // Create the correct user (no person_id lookup will conflict now)
                    $user = User::create($userData);
                    $results['new_users']++;
                    $matchInfo    = ['method' => 'corruption_recovery_created', 'matched_by' => null];
                    $nameMismatch = false; // fresh user — no mismatch
                    $dbName       = $csvName;
                }

                // Check if relationship already exists
                $relationshipExists = ActivityUser::where('user_id', $user->user_id)
                    ->where('activity_id', $activityId)
                    ->exists();

                if (!$relationshipExists) {
                    ActivityUser::create([
                        'activity_user_id' => (string) Str::uuid(),
                        'user_id'          => $user->user_id,
                        'activity_id'      => $activityId,
                        'cop_id'           => $copId,
                        'invited'          => $invitedDefault,
                        'attended'         => $attendedDefault ?? $attendedValue,
                        'type'             => $activityUserType,
                    ]);
                    $results['assigned']++;
                } else {
                    $results['already_assigned']++;

                    if ($nameMismatch) {
                        Log::error(
                            '[IMPORT-NAME-MISMATCH] Row ' . $rowNumber
                            . ' | CSV name: "' . $csvName . '"'
                            . ' | DB name on matched user: "' . $dbName . '"'
                            . ' | matched_by: ' . ($matchInfo['matched_by'] ?? 'unknown')
                            . ' | user_id: ' . $user->user_id
                            . ' | User is in activity but names differ — manual review needed'
                        );
                    }

                    // ── Collect for end-of-import list ──────────────────────────
                    $results['not_entered_names'][] = [
                        'row'       => $rowNumber,
                        'name'      => $csvName,
                        'db_name'   => $dbName,
                        'person_id' => $userData['person_id'] ?? 'N/A',
                        'phone'     => ($userData['phone_number'] ?? 'Not Provided') !== 'Not Provided' ? $userData['phone_number'] : 'N/A',
                        'reason'    => 'already assigned to this activity' . ($nameMismatch ? ' [NAME MISMATCH]' : ''),
                    ];

                    // ── Clean single-line log: name that did not enter ──────────
                    Log::warning(
                        '[IMPORT-NOT-ENTERED] Row ' . $rowNumber
                        . ' | Name: "' . $csvName . '"'
                        . ' | DB name: "' . $dbName . '"'
                        . ' | person_id: '   . ($userData['person_id']   ?? 'N/A')
                        . ' | istimara_id: ' . ($userData['istimara_id'] ?? 'N/A')
                        . ' | phone: '       . ($userData['phone_number'] !== 'Not Provided' ? $userData['phone_number'] : 'N/A')
                        . ' | Reason: already assigned to this activity'
                        . ' | matched_by: '  . ($matchInfo['matched_by'] ?? 'unknown')
                        . ($nameMismatch ? ' | *** NAME MISMATCH — manual review needed ***' : '')
                    );
                }

                // Release savepoint on success
                DB::statement("RELEASE SAVEPOINT {$savepoint}");

            } catch (\Exception $e) {
                // Rollback this row only
                DB::statement("ROLLBACK TO SAVEPOINT {$savepoint}");

                $results['failed']++;
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();

                $failedName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                if ($failedName === '') {
                    $failedName = '(name missing)';
                }

                // ── Collect for end-of-import list ──────────────────────────
                $results['not_entered_names'][] = [
                    'row'       => $rowNumber,
                    'name'      => $failedName,
                    'person_id' => $row['person_id'] ?? 'N/A',
                    'phone'     => 'N/A',
                    'reason'    => 'FAILED — ' . $e->getMessage(),
                ];

                // ── Clean single-line log: name that did not enter ──────────
                Log::error(
                    '[IMPORT-NOT-ENTERED] Row ' . $rowNumber
                    . ' | Name: "' . $failedName . '"'
                    . ' | person_id: ' . ($row['person_id'] ?? 'N/A')
                    . ' | Reason: FAILED — ' . $e->getMessage()
                );
            }
        }
    }
    
    /**
     * Optimized find or create user with caching
     */
    private function findOrCreateUserOptimized($userData, &$results, &$matchInfo = [])
    {
        static $userCache = [];

        // Build cache key from UNIQUE PERSONAL identifiers only.
        // istimara_id is a Lebanese household/family register number shared by all family members
        // — it must NEVER be used to identify a specific individual.
        $identifierParts = [];
        if (!empty($userData['person_id']))                                                    $identifierParts[] = 'pid:' . $userData['person_id'];
        if (!empty($userData['email']))                                                        $identifierParts[] = 'em:'  . strtolower(trim($userData['email']));
        if (!empty($userData['identification_id']))                                            $identifierParts[] = 'iid:' . $userData['identification_id'];
        if (!empty($userData['passport_number']))                                              $identifierParts[] = 'pp:'  . $userData['passport_number'];
        if (!empty($userData['phone_number']) && $userData['phone_number'] !== 'Not Provided') $identifierParts[] = 'ph:'  . $userData['phone_number'];

        $cacheKey = !empty($identifierParts) ? implode('|', $identifierParts) : null;

        if ($cacheKey && isset($userCache[$cacheKey])) {
            $matchInfo = ['method' => 'cache', 'matched_by' => $cacheKey];
            return $userCache[$cacheKey];
        }

        $user        = null;
        $matchedBy   = null;
        $matchStrength = null; // 'strong' | 'weak' — controls which fields we allow to overwrite

        // Priority-based lookup: each identifier tried independently, stopping at first hit.
        // Using orWhere causes cross-matches (e.g. row with person_id=X AND phone=Y finds a
        // DIFFERENT user who has phone=Y, marks them as already-assigned, and then corrupts
        // their person_id field — cascading into every future import run).
        if (!empty($userData['person_id'])) {
            $user = User::where('person_id', $userData['person_id'])->first();
            if ($user) { $matchedBy = 'person_id:' . $userData['person_id']; $matchStrength = 'strong'; }
        }

        if (!$user && !empty($userData['email'])) {
            $user = User::where('email', $userData['email'])->first();
            if ($user) { $matchedBy = 'email:' . $userData['email']; $matchStrength = 'strong'; }
        }

        if (!$user && !empty($userData['identification_id'])) {
            $user = User::where('identification_id', $userData['identification_id'])->first();
            if ($user) { $matchedBy = 'identification_id:' . $userData['identification_id']; $matchStrength = 'strong'; }
        }

        if (!$user && !empty($userData['passport_number'])) {
            $user = User::where('passport_number', $userData['passport_number'])->first();
            if ($user) { $matchedBy = 'passport_number:' . $userData['passport_number']; $matchStrength = 'strong'; }
        }

        if (!$user && !empty($userData['phone_number']) && $userData['phone_number'] !== 'Not Provided') {
            $user = User::where('phone_number', $userData['phone_number'])->first();
            if ($user) { $matchedBy = 'phone_number:' . $userData['phone_number']; $matchStrength = 'weak'; }
        }

        if ($user) {
            $matchInfo = ['method' => 'priority_lookup', 'matched_by' => $matchedBy];
        } else {
            if (empty($identifierParts)) {
                Log::warning('[IMPORT-NO-IDENTIFIERS] Row has no unique identifiers — falling back to name-only lookup', [
                    'first_name' => $userData['first_name'],
                    'last_name'  => $userData['last_name'],
                ]);
            }

            // Fallback: name-only match — same name ≠ same person, not cached, weak
            $user = User::where('first_name', $userData['first_name'])
                ->where('last_name', $userData['last_name'])
                ->first();

            if ($user) {
                $matchedBy     = "first_name='{$userData['first_name']}' last_name='{$userData['last_name']}'";
                $matchStrength = 'weak';
                $matchInfo     = ['method' => 'name_only_fallback', 'matched_by' => $matchedBy];
                Log::warning('[IMPORT-NAME-ONLY-MATCH] Matched by name only — risk of wrong person', [
                    'first_name'      => $userData['first_name'],
                    'last_name'       => $userData['last_name'],
                    'matched_user_id' => $user->user_id,
                ]);
            }
        }

        // Conflicting-identifier check for weak matches (phone, name fallback).
        // If the CSV carries a strong identifier (person_id, email, id-card, passport) AND the
        // DB user matched by phone/name has a DIFFERENT non-null value for that same field,
        // they are different people who happen to share a name or phone number.
        // Discard this match; the caller will create a new user instead.
        if ($user && $matchStrength === 'weak') {
            $conflicts = [];
            if (!empty($userData['person_id'])        && !empty($user->person_id)        && $user->person_id        != $userData['person_id'])        $conflicts[] = 'person_id';
            if (!empty($userData['email'])             && !empty($user->email)             && strtolower($user->email) != strtolower($userData['email'])) $conflicts[] = 'email';
            if (!empty($userData['identification_id']) && !empty($user->identification_id) && $user->identification_id != $userData['identification_id']) $conflicts[] = 'identification_id';
            if (!empty($userData['passport_number'])   && !empty($user->passport_number)   && $user->passport_number   != $userData['passport_number'])   $conflicts[] = 'passport_number';

            if (!empty($conflicts)) {
                Log::warning(
                    '[IMPORT-WEAK-MATCH-DISCARDED] Discarding weak match — conflicting identifiers indicate different people'
                    . ' | matched_by: ' . $matchedBy
                    . ' | conflicts: '  . implode(', ', $conflicts)
                    . ' | CSV person_id: ' . ($userData['person_id'] ?? 'N/A')
                    . ' | DB user_id: ' . $user->user_id
                    . ' | name: ' . ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '')
                );
                $user          = null;
                $matchedBy     = null;
                $matchStrength = null;
                $matchInfo     = [];
            }
        }

        if ($user) {
            // Update existing user with any new information from file
            $updated        = false;
            $updatedFields  = [];

            $fieldsToUpdate = [
                'phone_number', 'email', 'identification_id', 'passport_number',
                'person_id', 'istimara_id', 'middle_name', 'mother_name', 'dob',
                'gender', 'position_1', 'organization_1', 'organization_type_1',
                'status_1', 'address', 'sector',
            ];

            // Strong identifiers must never be written onto a user found by a weak match
            // (phone, name). Doing so corrupts the DB so future imports find the wrong person.
            $strongIdentifiers = ['person_id', 'email', 'identification_id', 'passport_number'];

            foreach ($fieldsToUpdate as $field) {
                if (!empty($userData[$field]) && (empty($user->$field) || $user->$field === 'Not Specified' || $user->$field === 'Not Provided')) {
                    // Skip writing a strong identifier onto a weakly-matched user
                    if ($matchStrength === 'weak' && in_array($field, $strongIdentifiers)) {
                        continue;
                    }
                    $updatedFields[$field] = ['from' => $user->$field, 'to' => $userData[$field]];
                    $user->$field = $userData[$field];
                    $updated = true;
                }
            }

            if ($updated) {
                $user->save();
                $results['merged_users']++;
                Log::info('[IMPORT-USER-MERGED] Updated existing user fields', [
                    'user_id'        => $user->user_id,
                    'updated_fields' => $updatedFields,
                ]);
            }

            $results['existing_users']++;
            if ($cacheKey) {
                $userCache[$cacheKey] = $user;
            }
            return $user;
        }

        // No match — create new user
        $user = User::create($userData);
        $results['new_users']++;
        $matchInfo = ['method' => 'created_new', 'matched_by' => null];
        Log::info('[IMPORT-USER-CREATED] New user created', [
            'user_id'    => $user->user_id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'person_id'  => $user->person_id ?? null,
        ]);

        // Handle nationality assignment
        if (!empty($userData['_nationality_id'])) {
            DB::table('users_nationality')->insert([
                'user_id'        => $user->user_id,
                'nationality_id' => $userData['_nationality_id'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // Handle diploma assignment
        if (!empty($userData['_diploma_id'])) {
            DB::table('users_diploma')->insert([
                'user_id'    => $user->user_id,
                'diploma_id' => $userData['_diploma_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($cacheKey) {
            $userCache[$cacheKey] = $user;
        }
        return $user;
    }
    
    /**
     * Convert Excel to CSV to save memory
     */
    private function convertExcelToCsv($file)
    {
        $inputFileType = IOFactory::identify($file->getPathname());
        $reader = IOFactory::createReader($inputFileType);
        
        // Set read data only to save memory
        $reader->setReadDataOnly(true);
        
        // Load only the first sheet
        $reader->setLoadSheetsOnly(null);
        
        // Load spreadsheet
        $spreadsheet = $reader->load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Create temp CSV file
        $tempCsvFile = tempnam(sys_get_temp_dir(), 'import_') . '.csv';
        $handle = fopen($tempCsvFile, 'w');
        
        // Get highest row and column
        $highestRow = min($worksheet->getHighestRow(), 50000); // Limit to 50,000 rows
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        // Write data to CSV
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (is_string($value)) {
                    $value = trim($value);
                }
                $rowData[] = $value;
            }
            fputcsv($handle, $rowData);
        }
        
        fclose($handle);
        
        // Clean up spreadsheet from memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();
        
        return $tempCsvFile;
    }
    
    /**
     * Build import response message
     */
    private function buildImportResponse($results)
    {
        $message = "✅ Import completed!\n";
        $message .= "✓ {$results['assigned']} users assigned to activity\n";
        $message .= "✓ {$results['new_users']} new users created\n";
        $message .= "✓ {$results['existing_users']} existing users found\n";
        
        if ($results['merged_users'] > 0) {
            $message .= "✓ {$results['merged_users']} users merged/updated\n";
        }
        
        if ($results['already_assigned'] > 0) {
            $message .= "ℹ️ {$results['already_assigned']} users were already assigned\n";
        }
        
        if ($results['duplicates'] > 0) {
            $message .= "⚠️ {$results['duplicates']} duplicate rows skipped\n";
        }
        
        if ($results['failed'] > 0) {
            $message .= "❌ {$results['failed']} rows failed\n";
        }
        
        $message .= "\n📊 Total processed: {$results['total']} rows";
        
        $messageType = ($results['failed'] > 0 || $results['duplicates'] > 0) ? 'warning' : 'success';
        
        $errorDetails = "";
        if (!empty($results['errors'])) {
            $errorDetails = implode('<br>', array_slice($results['errors'], 0, 20));
            if (count($results['errors']) > 20) {
                $errorDetails .= '<br>... and ' . (count($results['errors']) - 20) . ' more errors';
            }
        }
        
        if ($errorDetails) {
            return redirect()
                ->route('activity-users.index')
                ->with($messageType, $message)
                ->with('error_details', $errorDetails);
        }
        
        return redirect()
            ->route('activity-users.index')
            ->with($messageType, $message);
    }

    /**
     * Parse CSV file (kept for compatibility)
     */
    private function parseCSV($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if (!$handle) {
            throw new \Exception('Cannot open file');
        }
        
        $headers = fgetcsv($handle);
        if (!$headers) {
            throw new \Exception('Invalid CSV format');
        }
        
        $headers = array_map(function($header) {
            return trim(str_replace('*', '', $header));
        }, $headers);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }
            
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), '');
            }
            
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }
            $data[] = $rowData;
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Parse Excel file (kept for compatibility but not used directly)
     */
    private function parseExcel($file)
    {
        $data = [];
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            return $data;
        }
        
        $headers = array_map(function($header) {
            return trim(str_replace('*', '', $header));
        }, $rows[0]);
        
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) {
                continue;
            }
            
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }
            $data[] = $rowData;
        }
        
        return $data;
    }

    /**
     * Parse boolean value from string
     */
    private function parseBoolean($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        $value = strtolower(trim($value));
        $trueValues = ['true', 'yes', 'y', '1', 't', 'on', 'checked'];
        $falseValues = ['false', 'no', 'n', '0', 'f', 'off', 'unchecked'];
        
        if (in_array($value, $trueValues)) return true;
        if (in_array($value, $falseValues)) return false;
        return null;
    }

    /**
     * Prepare user data - WITH person_id and istimara_id
     */
    private function prepareUserData($row)
    {
        // ... (keep your existing prepareUserData method exactly as is)
        // Validate and sanitize first_name and last_name (MANDATORY)
        $firstName = trim($row['first_name'] ?? '');
        $lastName = trim($row['last_name'] ?? '');
        
        if (empty($firstName)) {
            throw new \Exception("first_name is required");
        }
        
        if (empty($lastName)) {
            throw new \Exception("last_name is required");
        }
        
        // Convert boolean strings
        $isHighProfile = false;
        if (isset($row['is_high_profile']) && !empty($row['is_high_profile'])) {
            $val = strtolower(trim($row['is_high_profile']));
            $isHighProfile = in_array($val, ['true', 'yes', '1', 't']);
        }
        
        // Handle scope - ALWAYS set a default (required field)
        $scope = 'National';
        if (!empty($row['scope'])) {
            $scope = trim($row['scope']);
            $scope = ucfirst(strtolower($scope));
            
            $scopeMap = [
                'int' => 'International',
                'internat' => 'International',
                'international' => 'International',
                'reg' => 'Regional',
                'regional' => 'Regional',
                'nat' => 'National',
                'national' => 'National',
                'loc' => 'Local',
                'local' => 'Local',
            ];
            
            $lowerScope = strtolower($scope);
            if (isset($scopeMap[$lowerScope])) {
                $scope = $scopeMap[$lowerScope];
            }
            
            if (!in_array($scope, ['International', 'Regional', 'National', 'Local'])) {
                Log::warning("Invalid scope value '{$scope}', defaulting to 'National'");
                $scope = 'National';
            }
        }
        
        // Handle gender - set to 'Not Specified' if empty
        $gender = 'Not Specified';
        if (!empty($row['gender'])) {
            $gender = trim($row['gender']);
            $gender = ucfirst(strtolower($gender));
            
            $genderMap = [
                'm' => 'Male',
                'male' => 'Male',
                'f' => 'Female',
                'female' => 'Female',
                'other' => 'Other',
                'not specified' => 'Not Specified',
                'not-specified' => 'Not Specified',
                'not_specified' => 'Not Specified',
                'ns' => 'Not Specified',
                'na' => 'Not Specified',
                'n/a' => 'Not Specified',
            ];
            
            $lowerGender = strtolower($gender);
            if (isset($genderMap[$lowerGender])) {
                $gender = $genderMap[$lowerGender];
            }
            
            if (!in_array($gender, ['Male', 'Female', 'Other', 'Not Specified'])) {
                $gender = 'Not Specified';
            }
        }
        
        // Handle organization_type_1 - set default
        $orgType1 = 'Private Sector';
        if (!empty($row['organization_type_1'])) {
            $orgType1 = trim($row['organization_type_1']);
            $orgType1 = ucwords(strtolower($orgType1));
            
            $orgMap = [
                'public sector' => 'Public Sector',
                'public' => 'Public Sector',
                'private sector' => 'Private Sector',
                'private' => 'Private Sector',
                'academia' => 'Academia',
                'academic' => 'Academia',
                'un' => 'UN',
                'united nations' => 'UN',
                'ingos' => 'INGOs',
                'ingo' => 'INGOs',
                'civil society' => 'Civil Society',
                'civil' => 'Civil Society',
                'ngos' => 'NGOs',
                'ngo' => 'NGOs',
                'activist' => 'Activist',
                'advocacy' => 'Activist',
            ];
            
            $lowerOrg = strtolower($orgType1);
            if (isset($orgMap[$lowerOrg])) {
                $orgType1 = $orgMap[$lowerOrg];
            }
            
            $allowedOrgTypes = ['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'];
            if (!in_array($orgType1, $allowedOrgTypes)) {
                $orgType1 = 'Private Sector';
            }
        }
        
        // Handle phone number
        $phoneNumber = $this->normalizePhone($row['phone_number'] ?? null);
        
        // Handle date of birth — Excel serial numbers (e.g. 30589) must be converted
        $dob = null;
        if (!empty($row['dob'])) {
            try {
                $dobRaw = trim($row['dob']);
                if (is_numeric($dobRaw) && (int)$dobRaw > 0 && (int)$dobRaw < 200000) {
                    // Excel date serial: days since 1899-12-30 (accounts for Excel's leap-year bug)
                    $dob = \Carbon\Carbon::create(1899, 12, 30)->addDays((int)$dobRaw)->format('Y-m-d');
                } else {
                    $dob = \Carbon\Carbon::parse($dobRaw)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                Log::warning("Invalid date format for dob: {$row['dob']}");
                $dob = null;
            }
        }
        
        // Handle organization_type_2 if provided
        $orgType2 = null;
        if (!empty($row['organization_type_2'])) {
            $orgType2 = trim($row['organization_type_2']);
            $orgType2 = ucwords(strtolower($orgType2));
            $lowerOrg2 = strtolower($orgType2);
            $orgMap = [
                'public sector' => 'Public Sector',
                'private sector' => 'Private Sector',
                'academia' => 'Academia',
                'un' => 'UN',
                'ingos' => 'INGOs',
                'civil society' => 'Civil Society',
                'ngos' => 'NGOs',
                'activist' => 'Activist',
            ];
            if (isset($orgMap[$lowerOrg2])) {
                $orgType2 = $orgMap[$lowerOrg2];
            }
            $allowedOrgTypes = ['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'];
            if (!in_array($orgType2, $allowedOrgTypes)) {
                $orgType2 = null;
            }
        }
        
        // Handle default_cop_id (UUID, keep as string)
        $defaultCopId = null;
        if (!empty($row['default_cop_id'])) {
            $defaultCopId = trim($row['default_cop_id']);
            if ($defaultCopId === '') {
                $defaultCopId = null;
            }
        }
        
        // Handle position_1 - set default if not provided
        $position1 = trim($row['position_1'] ?? '');
        if (empty($position1)) {
            $position1 = 'Not Specified';
        }
        
        // Handle organization_1 - set default if not provided
        $organization1 = trim($row['organization_1'] ?? '');
        if (empty($organization1)) {
            $organization1 = 'Not Specified';
        }
        
        // Handle status_1 - set default if not provided
        $status1 = trim($row['status_1'] ?? '');
        if (empty($status1)) {
            $status1 = 'Active';
        }
        
        // Handle address - set default if not provided
        $address = trim($row['address'] ?? '');
        if (empty($address)) {
            $address = 'Not Provided';
        }
        
        // Handle nationality - find or create nationality
        $nationalityId = null;
        if (!empty($row['nationality_name'])) {
            $nationalityName = trim($row['nationality_name']);
            
            // Try to find existing nationality
            $nationality = Nationality::where('name', $nationalityName)->first();
            
            if (!$nationality) {
                // Create new nationality - model will auto-generate the UUID
                $nationality = Nationality::create([
                    'name' => $nationalityName,
                ]);
                Log::info("Created new nationality: {$nationalityName}", ['nationality_id' => $nationality->nationality_id]);
            }
            $nationalityId = $nationality->nationality_id;
        }
        
        // Handle diploma - find or create diploma
        $diplomaId = null;
        if (!empty($row['diploma_name'])) {
            $diplomaName = trim($row['diploma_name']);
            $institution = $row['diploma_institution'] ?? null;
            $year = $row['diploma_year'] ?? null;
            
            // Try to find existing diploma by name
            $diploma = Diploma::where('diploma_name', $diplomaName)->first();
            
            if (!$diploma) {
                // Create new diploma - model will auto-generate the UUID
                $diplomaData = [
                    'diploma_name' => $diplomaName,
                ];
                
                // Add optional fields if provided
                if ($institution) {
                    $diplomaData['institution'] = $institution;
                }
                if ($year) {
                    $diplomaData['year'] = $year;
                }
                
                $diploma = Diploma::create($diplomaData);
                Log::info("Created new diploma: {$diplomaName}", ['diploma_id' => $diploma->diploma_id]);
            } else {
                // Update existing diploma with missing info if needed
                $updated = false;
                if ($institution && empty($diploma->institution)) {
                    $diploma->institution = $institution;
                    $updated = true;
                }
                if ($year && empty($diploma->year)) {
                    $diploma->year = $year;
                    $updated = true;
                }
                if ($updated) {
                    $diploma->save();
                    Log::info("Updated existing diploma: {$diplomaName}");
                }
            }
            $diplomaId = $diploma->diploma_id;
        }
        
        // Build user data array
        $userData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'scope' => $scope,
            'is_high_profile' => $isHighProfile,
            'gender' => $gender,
            'position_1' => $position1,
            'organization_1' => $organization1,
            'organization_type_1' => $orgType1,
            'status_1' => $status1,
            'address' => $address,
        ];
        
        // Add phone number if provided
        if ($phoneNumber) {
            $userData['phone_number'] = $phoneNumber;
        } else {
            $userData['phone_number'] = 'Not Provided';
        }
        
        // Add optional fields only if they have values
        if (!empty($row['sector'])) $userData['sector'] = $row['sector'];
        if (!empty($row['middle_name'])) $userData['middle_name'] = $row['middle_name'];
        if (!empty($row['mother_name'])) $userData['mother_name'] = $row['mother_name'];
        if (!empty($row['office_phone'])) $userData['office_phone'] = $this->normalizePhone($row['office_phone']);
        if (!empty($row['extension_number'])) $userData['extension_number'] = $row['extension_number'];
        if (!empty($row['home_phone'])) $userData['home_phone'] = $this->normalizePhone($row['home_phone']);
        if (!empty($row['email'])) $userData['email'] = strtolower(trim($row['email']));
        if (!empty($row['position_2'])) $userData['position_2'] = $row['position_2'];
        if (!empty($row['organization_2'])) $userData['organization_2'] = $row['organization_2'];
        if ($orgType2) $userData['organization_type_2'] = $orgType2;
        if (!empty($row['status_2'])) $userData['status_2'] = $row['status_2'];
        if (!empty($row['identification_id'])) $userData['identification_id'] = $row['identification_id'];
        if (!empty($row['register_number'])) $userData['register_number'] = $row['register_number'];
        if (!empty($row['marital_status'])) $userData['marital_status'] = $row['marital_status'];
        if (!empty($row['employment_status'])) $userData['employment_status'] = $row['employment_status'];
        if (!empty($row['passport_number'])) $userData['passport_number'] = $row['passport_number'];
        if (!empty($row['register_place'])) $userData['register_place'] = $row['register_place'];
        if ($defaultCopId) $userData['default_cop_id'] = $defaultCopId;
        if ($dob) $userData['dob'] = $dob;
        if (!empty($row['prefix'])) $userData['prefix'] = $row['prefix'];
        
        // NEW: Add person_id and istimara_id
        if (!empty($row['person_id'])) $userData['person_id'] = trim($row['person_id']);
        if (!empty($row['istimara_id'])) $userData['istimara_id'] = trim($row['istimara_id']);
        
        // Add nationality and diploma IDs for pivot table assignment
        $userData['_nationality_id'] = $nationalityId;
        $userData['_diploma_id'] = $diplomaId;
        
        return $userData;
    }
    
    /**
     * Helper methods for normalization
     */
    private function normalizeScope($scope)
    {
        $scope = trim(ucfirst(strtolower($scope)));
        $allowed = ['International', 'Regional', 'National', 'Local'];
        return in_array($scope, $allowed) ? $scope : 'National';
    }

    private function normalizeGender($gender)
    {
        $gender = strtolower(trim($gender));
        if (in_array($gender, ['m', 'male'])) return 'Male';
        if (in_array($gender, ['f', 'female'])) return 'Female';
        return 'Other';
    }

    private function normalizeOrganizationType($type)
    {
        $type = trim($type);
        $map = [
            'public sector' => 'Public Sector',
            'private sector' => 'Private Sector',
            'academia' => 'Academia',
            'un' => 'UN',
            'ingos' => 'INGOs',
            'civil society' => 'Civil Society',
            'ngos' => 'NGOs',
            'activist' => 'Activist',
        ];
        
        $lower = strtolower($type);
        return $map[$lower] ?? $type;
    }

    private function normalizePhone($phone)
    {
        if (empty($phone)) return null;
        
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Format Lebanese numbers
        if (preg_match('/^(03|70|71|76|78|79|81)(\d{6})$/', $phone, $matches)) {
            return '+961 ' . $matches[1] . ' ' . substr($matches[2], 0, 3) . ' ' . substr($matches[2], 3, 3);
        }
        
        return $phone;
    }

    private function normalizeUserType($type)
    {
        $type = ucfirst(strtolower(trim($type)));
        return in_array($type, ['Stakeholder', 'Beneficiary']) ? $type : null;
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'start_date']);
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        
        return view('activity-users.import', compact('activities', 'cops'));
    }
}