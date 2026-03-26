<?php
namespace App\Http\Controllers;

use App\Models\ActivityUser;
use App\Models\Activity;
use App\Models\User;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
class ActivityUserController extends Controller
{
    /**
     * Display a listing of activity-user relationships.
     */
    public function index(Request $request)
{
    $query = ActivityUser::with(['user', 'activity', 'cop'])
        ->orderBy('created_at', 'desc');

    // Filter by activity if provided
    if ($request->filled('activity_id')) {
        $query->where('activity_id', $request->activity_id);
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
    $query->whereHas('user', function ($userQuery) use ($type) {
        $userQuery->where('type', $type);
    });
}

    // Filter by invited status
    if ($request->has('invited') && $request->invited !== '') {
        $query->where('invited', $request->boolean('invited'));
    }

    // Filter by attended status
    if ($request->has('attended') && $request->attended !== '') {
        $query->where('attended', $request->boolean('attended'));
    }
    
    // NEW: Filter by activity start date
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
                          ->orWhere('register_number', 'ilike', "%{$search}%");
            })
            ->orWhereHas('activity', function ($activityQuery) use ($search) {
                $activityQuery->where('activity_title_en', 'ilike', "%{$search}%")
                              ->orWhere('activity_title_ar', 'ilike', "%{$search}%");
            });
        });
    }
    
    // NEW: User-specific search
 if ($request->filled('user_search')) {
    $userSearch = $request->user_search;
    
    $query->whereHas('user', function ($userQuery) use ($userSearch) {
        $userQuery->where(function($q) use ($userSearch) {
            // Match first name OR last name containing the search term
            $q->where('first_name', 'ilike', "%{$userSearch}%")
              ->orWhere('middle_name', 'ilike', "%{$userSearch}%")
              ->orWhere('last_name', 'ilike', "%{$userSearch}%")
              ->orWhere('email', 'ilike', "%{$userSearch}%")
              ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'ilike', "%{$userSearch}%")
              ->orWhere(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'ilike', "%{$userSearch}%");
        });
    });
}
    // NEW: Activity-specific search
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

    // Handle pagination
    $perPage = $request->get('per_page', 20);
    $activityUsers = $query->paginate($perPage);

    // Get data for filter dropdowns
    $activities = Activity::orderBy('activity_title_en')
        ->limit(100)
        ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
    
    $users = User::orderBy('first_name')
        ->limit(100)
        ->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email']);
    
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
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->limit(100)->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email']);

        return view('activity-users.create', compact('cops', 'activities', 'users'));
    }

    /**
     * Store a newly created activity-user relationship in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_id' => 'required|exists:activities,activity_id',
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255',
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
        $activityUser = ActivityUser::findOrFail($id);
        $cops = Cop::orderBy('cop_name')->get(['cop_id', 'cop_name']);
        $activities = Activity::orderBy('activity_title_en')->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email']);

        // IMPORTANT: Pass the $id to the view
    return view('activity-users.edit', compact('activityUser', 'cops', 'activities', 'users', 'id'));
    }

    /**
     * Update the specified activity-user relationship in storage.
     */
    public function update(Request $request, $id)
{
    $activityUser = ActivityUser::findOrFail($id);

    $validated = $request->validate([
        'user_id' => 'required|exists:users,user_id',        // ← ADD THIS
        'activity_id' => 'required|exists:activities,activity_id', // ← ADD THIS
        'cop_id' => 'nullable|exists:cops,cop_id',
        'type' => 'nullable|string|max:255',
        'invited' => 'sometimes|boolean',
        'attended' => 'sometimes|boolean',
    ]);

    // Set default values for checkboxes if not present
    $validated['invited'] = $request->boolean('invited', false);
    $validated['attended'] = $request->boolean('attended', false);

    try {
        DB::beginTransaction();
        
        $activityUser->update($validated); // Now includes user_id and activity_id
        
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
        $query = ActivityUser::with(['user', 'activity', 'cop']);

        // Apply filters if provided
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
                $item->user ? $item->user->type : '',
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
            'gender*',
            'position_1*',
            'organization_1*',
            'organization_type_1*',
            'status_1*',
            'address*',
            'phone_number*',
            'scope*',
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
            'role_in_activity'  // Additional field for role in this activity
        ]);
        
        // Example data row
        fputcsv($file, [
            'John',                         // first_name*
            'Doe',                          // last_name*
            'Male',                         // gender*
            'Senior Manager',               // position_1*
            'Beirut Medical Center',        // organization_1*
            'Public Sector',                // organization_type_1*
            'Active',                       // status_1*
            'Beirut, Lebanon',              // address*
            '+961 70 123 456',              // phone_number*
            'National',                     // scope*
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
            'Stakeholder',                  // type
            '123e4567-e89b-12d3-a456-426614174000', // default_cop_id
            'Participant'                   // role_in_activity
        ]);
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}


/**
 * Process the import file
 */
public function import(Request $request)
{
    $request->validate([
        'activity_id' => 'required|exists:activities,activity_id',
        'import_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
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
        'total' => 0,
        'new_users' => 0,
        'existing_users' => 0,
        'assigned' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    try {
        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();
        
        $data = [];
        
        if ($extension === 'csv') {
            $data = $this->parseCSV($file);
        } else {
            $data = $this->parseExcel($file);
        }
        
        if (empty($data)) {
            throw new \Exception('No data found in file');
        }
        
        DB::beginTransaction();
        
        foreach ($data as $rowIndex => $row) {
            $results['total']++;
            $rowNumber = $rowIndex + 2; // +2 for header and 1-based index
            
            try {
                // Validate required fields
                $requiredFields = ['first_name', 'last_name', 'gender', 'position_1', 
                                   'organization_1', 'organization_type_1', 'status_1', 
                                   'address', 'phone_number', 'scope'];
                
                $missingFields = [];
                foreach ($requiredFields as $field) {
                    if (empty($row[$field]) && $row[$field] !== '0') {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    throw new \Exception("Missing required fields: " . implode(', ', $missingFields));
                }
                
                // Prepare user data
                $userData = $this->prepareUserData($row, $defaultUserType);
                
                // Validate unique fields
                $existingUser = null;
                
                // Check by email first if provided
                if (!empty($userData['email'])) {
                    $existingUser = User::where('email', $userData['email'])->first();
                }
                
                // If not found by email, check by identification_id
                if (!$existingUser && !empty($userData['identification_id'])) {
                    $existingUser = User::where('identification_id', $userData['identification_id'])->first();
                }
                
                // If not found, check by passport_number
                if (!$existingUser && !empty($userData['passport_number'])) {
                    $existingUser = User::where('passport_number', $userData['passport_number'])->first();
                }
                
                // If not found, check by name + phone combination
                if (!$existingUser) {
                    $existingUser = User::where('first_name', $userData['first_name'])
                        ->where('last_name', $userData['last_name'])
                        ->where('phone_number', $userData['phone_number'])
                        ->first();
                }
                
                if ($existingUser) {
                    $results['existing_users']++;
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create($userData);
                    $results['new_users']++;
                }
                
                // Check if relationship already exists
                $exists = ActivityUser::where('user_id', $user->user_id)
                    ->where('activity_id', $activityId)
                    ->exists();
                
                if (!$exists) {
                    // Create activity user relationship
                    $activityUserData = [
                        'activity_user_id' => (string) Str::uuid(),
                        'user_id' => $user->user_id,
                        'activity_id' => $activityId,
                        'invited' => $invitedDefault,
                        'attended' => $attendedDefault,
                        'type' => $row['role_in_activity'] ?? null,
                    ];
                    
                    if ($copId) {
                        $activityUserData['cop_id'] = $copId;
                    }
                    
                    ActivityUser::create($activityUserData);
                    $results['assigned']++;
                } else {
                    // Already assigned, update if needed
                    $activityUser = ActivityUser::where('user_id', $user->user_id)
                        ->where('activity_id', $activityId)
                        ->first();
                    
                    $updated = false;
                    
                    if ($invitedDefault && !$activityUser->invited) {
                        $activityUser->invited = true;
                        $updated = true;
                    }
                    
                    if ($attendedDefault && !$activityUser->attended) {
                        $activityUser->attended = true;
                        $updated = true;
                    }
                    
                    if (!empty($row['role_in_activity']) && empty($activityUser->type)) {
                        $activityUser->type = $row['role_in_activity'];
                        $updated = true;
                    }
                    
                    if ($copId && empty($activityUser->cop_id)) {
                        $activityUser->cop_id = $copId;
                        $updated = true;
                    }
                    
                    if ($updated) {
                        $activityUser->save();
                    }
                    
                    $results['assigned']++;
                }
                
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("Import error at row {$rowNumber}: " . $e->getMessage());
            }
        }
        
        DB::commit();
        
        return $this->handleImportResults($results);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Import failed: ' . $e->getMessage());
        
        return redirect()->route('activity-users.import.form')
            ->with('error', 'Failed to process file: ' . $e->getMessage());
    }
}

/**
 * Parse CSV file - MATCHES UserController format
 */
private function parseCSV($file)
{
    $data = [];
    $handle = fopen($file->getPathname(), 'r');
    
    if (!$handle) {
        throw new \Exception('Cannot open file');
    }
    
    // Get headers
    $headers = fgetcsv($handle);
    if (!$headers) {
        throw new \Exception('Invalid CSV format');
    }
    
    // Clean headers (remove * and trim)
    $headers = array_map(function($header) {
        return trim(str_replace('*', '', $header));
    }, $headers);
    
    Log::info('CSV Headers', ['headers' => $headers]);
    
    while (($row = fgetcsv($handle)) !== false) {
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Pad row if needed
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
    Log::info('CSV parsed', ['row_count' => count($data)]);
    
    return $data;
}
/**
 * Parse Excel file
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
    
    // Clean headers (remove * and trim)
    $headers = array_map(function($header) {
        return trim(str_replace('*', '', $header));
    }, $rows[0]);
    
    Log::info('Excel Headers', ['headers' => $headers]);
    
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        $rowData = [];
        foreach ($headers as $index => $header) {
            $rowData[$header] = $row[$index] ?? '';
        }
        $data[] = $rowData;
    }
    
    Log::info('Excel parsed', ['row_count' => count($data)]);
    
    return $data;
}
/**
 * Prepare user data for import - MATCHES UserController's cleanImportData
 */
private function prepareUserData($row, $defaultUserType = null)
{
    // Debug: Log incoming row
    Log::info('Preparing user data', ['row_keys' => array_keys($row)]);
    
    // Convert boolean strings
    $isHighProfile = false;
    if (isset($row['is_high_profile']) && !empty($row['is_high_profile'])) {
        $val = strtolower(trim($row['is_high_profile']));
        $isHighProfile = in_array($val, ['true', 'yes', '1', 't']);
    }
    
    // Handle scope with proper normalization (matching UserController)
    $scope = $row['scope'] ?? 'National';
    $scope = trim($scope);
    $scope = ucfirst(strtolower($scope));
    
    // Fix common typos
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
    
    // Handle gender with proper normalization
    $gender = $row['gender'] ?? 'Male';
    $gender = trim($gender);
    $gender = ucfirst(strtolower($gender));
    
    $genderMap = [
        'm' => 'Male',
        'male' => 'Male',
        'f' => 'Female',
        'female' => 'Female',
    ];
    
    $lowerGender = strtolower($gender);
    if (isset($genderMap[$lowerGender])) {
        $gender = $genderMap[$lowerGender];
    }
    
    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        Log::warning("Invalid gender value '{$gender}', defaulting to 'Male'");
        $gender = 'Male';
    }
    
    // Handle organization_type_1
    $orgType1 = $row['organization_type_1'] ?? 'Private Sector';
    $orgType1 = trim($orgType1);
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
        Log::warning("Invalid organization type '{$orgType1}', defaulting to 'Private Sector'");
        $orgType1 = 'Private Sector';
    }
    
    // Handle phone number
    $phoneNumber = $this->normalizePhone($row['phone_number'] ?? null);
    
    // Handle date of birth
    $dob = null;
    if (!empty($row['dob'])) {
        try {
            $dob = \Carbon\Carbon::parse($row['dob'])->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Invalid date format for dob: {$row['dob']}");
            $dob = null;
        }
    }
    
    // Handle type (Stakeholder/Beneficiary)
    $type = null;
    if (!empty($row['type'])) {
        $type = ucfirst(strtolower(trim($row['type'])));
        $allowedTypes = ['Stakeholder', 'Beneficiary'];
        if (!in_array($type, $allowedTypes)) {
            Log::warning("Invalid type value '{$type}', setting to null");
            $type = null;
        }
    } elseif ($defaultUserType) {
        $type = $defaultUserType;
    }
    
    // Handle organization_type_2 if provided
    $orgType2 = null;
    if (!empty($row['organization_type_2'])) {
        $orgType2 = trim($row['organization_type_2']);
        $orgType2 = ucwords(strtolower($orgType2));
        $lowerOrg2 = strtolower($orgType2);
        if (isset($orgMap[$lowerOrg2])) {
            $orgType2 = $orgMap[$lowerOrg2];
        }
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
    
    // Build user data array - MATCHING UserController structure
    $userData = [
        'prefix' => $row['prefix'] ?? null,
        'is_high_profile' => $isHighProfile,
        'scope' => $scope,
        'first_name' => trim($row['first_name'] ?? ''),
        'last_name' => trim($row['last_name'] ?? ''),
        'gender' => $gender,
        'position_1' => trim($row['position_1'] ?? ''),
        'organization_1' => trim($row['organization_1'] ?? ''),
        'organization_type_1' => $orgType1,
        'status_1' => trim($row['status_1'] ?? ''),
        'address' => trim($row['address'] ?? ''),
        'phone_number' => $phoneNumber,
        'sector' => $row['sector'] ?? null,
        'middle_name' => $row['middle_name'] ?? null,
        'mother_name' => $row['mother_name'] ?? null,
        'office_phone' => $this->normalizePhone($row['office_phone'] ?? null),
        'home_phone' => $this->normalizePhone($row['home_phone'] ?? null),
        'email' => !empty($row['email']) ? strtolower(trim($row['email'])) : null,
        'position_2' => $row['position_2'] ?? null,
        'organization_2' => $row['organization_2'] ?? null,
        'organization_type_2' => $orgType2,
        'status_2' => $row['status_2'] ?? null,
        'identification_id' => $row['identification_id'] ?? null,
        'register_number' => $row['register_number'] ?? null,
        'marital_status' => $row['marital_status'] ?? null,
        'employment_status' => $row['employment_status'] ?? null,
        'passport_number' => $row['passport_number'] ?? null,
        'register_place' => $row['register_place'] ?? null,
        'default_cop_id' => $defaultCopId,
        'dob' => $dob,
        'type' => $type,
    ];
    
    // Remove any null values that should not be passed (optional)
    $userData = array_filter($userData, function($value) {
        return $value !== null;
    });
    
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
 * Handle import results
 */
private function handleImportResults($results)
{
    $message = sprintf(
        "Import completed: %d users processed. %d new users created, %d existing users found, %d assigned to activity, %d failed.",
        $results['total'],
        $results['new_users'],
        $results['existing_users'],
        $results['assigned'],
        $results['failed']
    );
    
    if ($results['failed'] > 0) {
        $errorDetails = implode('<br>', array_slice($results['errors'], 0, 10));
        if (count($results['errors']) > 10) {
            $errorDetails .= '<br>... and ' . (count($results['errors']) - 10) . ' more errors';
        }
        
        return redirect()
            ->route('activity-users.import.form')
            ->with('warning', $message)
            ->with('error_details', $errorDetails);
    }
    
    return redirect()
        ->route('activity-users.index')
        ->with('success', $message);
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