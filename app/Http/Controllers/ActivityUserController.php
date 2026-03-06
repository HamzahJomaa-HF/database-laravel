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

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by lead status
        if ($request->has('is_lead') && $request->is_lead !== '') {
            $query->where('is_lead', $request->boolean('is_lead'));
        }

        // Filter by invited status
        if ($request->has('invited') && $request->invited !== '') {
            $query->where('invited', $request->boolean('invited'));
        }

        // Filter by attended status
        if ($request->has('attended') && $request->attended !== '') {
            $query->where('attended', $request->boolean('attended'));
        }

        // Search by user or activity names
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

        // Handle pagination
        $perPage = $request->get('per_page', 20);
        $activityUsers = $query->paginate($perPage);

        // Get data for filter dropdowns
        $activities = Activity::orderBy('activity_title_en')
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        
        $users = User::orderBy('first_name')
            ->get(['user_id', 'first_name', 'middle_name', 'last_name', 'email']);
        
        $cops = Cop::orderBy('cop_name')
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
            'is_lead' => 'sometimes|boolean',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
            'external_id' => 'nullable|string|max:255|unique:activity_users,external_id',
        ]);

        // Set default values for checkboxes if not present
        $validated['is_lead'] = $request->boolean('is_lead', false);
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
     * Display the specified activity-user relationship.
     */
    public function show($id)
    {
        $activityUser = ActivityUser::with(['user', 'activity', 'cop'])
            ->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $activityUser
            ]);
        }

        return view('activity-users.show', compact('activityUser'));
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

        return view('activity-users.edit', compact('activityUser', 'cops', 'activities', 'users'));
    }

    /**
     * Update the specified activity-user relationship in storage.
     */
    public function update(Request $request, $id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        $validated = $request->validate([
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255',
            'is_lead' => 'sometimes|boolean',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
            'external_id' => 'nullable|string|max:255|unique:activity_users,external_id,' . $id . ',activity_user_id',
        ]);

        // Set default values for checkboxes if not present
        $validated['is_lead'] = $request->boolean('is_lead', false);
        $validated['invited'] = $request->boolean('invited', false);
        $validated['attended'] = $request->boolean('attended', false);

        try {
            DB::beginTransaction();
            
            $activityUser->update($validated);
            
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Activity-User relationship updated successfully!',
                    'data' => $activityUser->load(['user', 'activity', 'cop'])
                ]);
            }

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update activity-user relationship: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update relationship: ' . $e->getMessage()
                ], 500);
            }

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
     * Display a listing of trashed (soft-deleted) activity-user relationships.
     */
    public function trash(Request $request)
    {
        $deletedActivityUsers = ActivityUser::onlyTrashed()
            ->with(['user', 'activity', 'cop'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $deletedActivityUsers
            ]);
        }

        return view('activity-users.trash', compact('deletedActivityUsers'));
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
     * Toggle attendance status.
     */
    public function toggleAttendance($id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->update([
                'attended' => !$activityUser->attended
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'attended' => $activityUser->attended,
                'message' => 'Attendance status updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle invited status.
     */
    public function toggleInvited($id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->update([
                'invited' => !$activityUser->invited
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'invited' => $activityUser->invited,
                'message' => 'Invitation status updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invitation status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle lead status.
     */
    public function toggleLead($id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->update([
                'is_lead' => !$activityUser->is_lead
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'is_lead' => $activityUser->is_lead,
                'message' => 'Lead status updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead status: ' . $e->getMessage()
            ], 500);
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

        if ($request->has('is_lead') && $request->is_lead !== '') {
            $query->where('is_lead', $request->boolean('is_lead'));
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
            'Is Lead',
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
                $item->is_lead ? 'Yes' : 'No',
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
}