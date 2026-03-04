<?php
namespace App\Http\Controllers;

use App\Models\ActivityUser;
use App\Models\Activity;
use App\Models\User;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        if ($request->has('activity_id') && $request->activity_id) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by cop if provided
        if ($request->has('cop_id') && $request->cop_id) {
            $query->where('cop_id', $request->cop_id);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by lead status
        if ($request->has('is_lead') && $request->is_lead !== '') {
            $query->where('is_lead', $request->is_lead);
        }

        // Filter by invited status
        if ($request->has('invited') && $request->invited !== '') {
            $query->where('invited', $request->invited);
        }

        // Filter by attended status
        if ($request->has('attended') && $request->attended !== '') {
            $query->where('attended', $request->attended);
        }

        // Search by external_id or user/activity names
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('external_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('activity', function ($activityQuery) use ($search) {
                      $activityQuery->where('activity_title_en', 'like', "%{$search}%")
                                    ->orWhere('activity_title_ar', 'like', "%{$search}%");
                  });
            });
        }

        $activityUsers = $query->paginate(20);

        // Get data for filter dropdowns
        $activities = Activity::orderBy('activity_title_en')
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')
            ->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')
            ->get(['cop_id', 'cop_name']);

        // Get distinct types for filter dropdown
        $types = ActivityUser::distinct()->pluck('type')->filter()->values();

        return view('activity-users.index', compact('activityUsers', 'activities', 'users', 'cops', 'types'));
    }

    /**
     * Show the form for creating a new activity-user relationship.
     */
    public function create()
    {
        $activities = Activity::orderBy('activity_title_en')
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar', 'start_date', 'end_date']);
        $users = User::orderBy('first_name')
            ->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')
            ->get(['cop_id', 'cop_name']);

        return view('activity-users.create', compact('activities', 'users', 'cops'));
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
        $validated['is_lead'] = $request->has('is_lead');
        $validated['invited'] = $request->has('invited');
        $validated['attended'] = $request->has('attended');

        // Check if relationship already exists
        $exists = ActivityUser::where('user_id', $validated['user_id'])
            ->where('activity_id', $validated['activity_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'This user is already assigned to this activity.'])->withInput();
        }

        try {
            DB::beginTransaction();
            
            $activityUser = ActivityUser::create($validated);
            
            DB::commit();

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create relationship: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified activity-user relationship.
     */
    public function show($id)
    {
        $activityUser = ActivityUser::with(['user', 'activity', 'cop'])
            ->findOrFail($id);

        return view('activity-users.show', compact('activityUser'));
    }

    /**
     * Show the form for editing the specified activity-user relationship.
     */
    public function edit($id)
    {
        $activityUser = ActivityUser::findOrFail($id);
        
        $activities = Activity::orderBy('activity_title_en')
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar', 'start_date', 'end_date']);
        $users = User::orderBy('first_name')
            ->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')
            ->get(['cop_id', 'cop_name']);

        return view('activity-users.edit', compact('activityUser', 'activities', 'users', 'cops'));
    }

    /**
     * Update the specified activity-user relationship in storage.
     */
    public function update(Request $request, $id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'activity_id' => 'required|exists:activities,activity_id',
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255',
            'is_lead' => 'sometimes|boolean',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
            'external_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('activity_users', 'external_id')->ignore($activityUser->activity_user_id, 'activity_user_id')
            ],
        ]);

        // Set default values for checkboxes if not present
        $validated['is_lead'] = $request->has('is_lead');
        $validated['invited'] = $request->has('invited');
        $validated['attended'] = $request->has('attended');

        // Check if relationship already exists (excluding current record)
        $exists = ActivityUser::where('user_id', $validated['user_id'])
            ->where('activity_id', $validated['activity_id'])
            ->where('activity_user_id', '!=', $activityUser->activity_user_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'This user is already assigned to this activity.'])->withInput();
        }

        try {
            DB::beginTransaction();
            
            $activityUser->update($validated);
            
            DB::commit();

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update relationship: ' . $e->getMessage())->withInput();
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

            return redirect()->route('activity-users.index')
                ->with('success', 'Activity-User relationship deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete relationship: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified activity-user relationship.
     */
    public function softDelete($id)
    {
        $activityUser = ActivityUser::findOrFail($id);

        try {
            $activityUser->delete();
            return back()->with('success', 'Activity-User relationship moved to trash successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete relationship: ' . $e->getMessage());
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
            return back()->with('success', 'Activity-User relationship restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore relationship: ' . $e->getMessage());
        }
    }

    /**
     * Force delete an activity-user relationship.
     */
    public function forceDelete($id)
    {
        $activityUser = ActivityUser::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();
            
            $activityUser->forceDelete();
            
            DB::commit();

            return redirect()->route('activity-users.trash')
                ->with('success', 'Activity-User relationship permanently deleted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to permanently delete relationship: ' . $e->getMessage());
        }
    }

    /**
     * Display a list of soft-deleted activity-user relationships.
     */
    public function trash()
    {
        $deletedActivityUsers = ActivityUser::onlyTrashed()
            ->with(['user', 'activity', 'cop'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('activity-users.trash', compact('deletedActivityUsers'));
    }

    /**
     * Show bulk assignment form.
     */
    public function showBulkForm()
    {
        $activities = Activity::orderBy('activity_title_en')
            ->get(['activity_id', 'activity_title_en', 'activity_title_ar']);
        $users = User::orderBy('first_name')
            ->get(['user_id', 'first_name', 'last_name', 'email']);
        $cops = Cop::orderBy('cop_name')
            ->get(['cop_id', 'cop_name']);

        return view('activity-users.bulk', compact('activities', 'users', 'cops'));
    }

    /**
     * Bulk assign users to an activity.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
            'cop_id' => 'nullable|exists:cops,cop_id',
            'type' => 'nullable|string|max:255',
            'is_lead' => 'sometimes|boolean',
            'invited' => 'sometimes|boolean',
            'attended' => 'sometimes|boolean',
        ]);

        $created = 0;
        $skipped = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            foreach ($validated['user_ids'] as $userId) {
                // Check if relationship already exists
                $exists = ActivityUser::where('user_id', $userId)
                    ->where('activity_id', $validated['activity_id'])
                    ->exists();

                if (!$exists) {
                    ActivityUser::create([
                        'user_id' => $userId,
                        'activity_id' => $validated['activity_id'],
                        'cop_id' => $validated['cop_id'] ?? null,
                        'type' => $validated['type'] ?? null,
                        'is_lead' => $request->has('is_lead'),
                        'invited' => $request->has('invited'),
                        'attended' => $request->has('attended'),
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            $message = "{$created} users assigned to activity successfully.";
            if ($skipped > 0) {
                $message .= " {$skipped} users were already assigned (skipped).";
            }

            return redirect()->route('activity-users.index', ['activity_id' => $validated['activity_id']])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to bulk assign users: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update attendance status for multiple users.
     */
    public function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*' => 'boolean',
        ]);

        $updated = 0;

        try {
            DB::beginTransaction();

            foreach ($validated['attendances'] as $activityUserId => $attended) {
                $activityUser = ActivityUser::find($activityUserId);
                if ($activityUser) {
                    $activityUser->update(['attended' => $attended]);
                    $updated++;
                }
            }

            DB::commit();

            return back()->with('success', "Attendance updated for {$updated} users successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update attendance: ' . $e->getMessage());
        }
    }

    /**
     * Export activity users to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityUser::with(['user', 'activity', 'cop']);

        // Apply filters if provided
        if ($request->has('activity_id') && $request->activity_id) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $activityUsers = $query->get();

        $filename = 'activity-users-' . now()->format('Y-m-d-His') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add headers
        fputcsv($handle, [
            'External ID',
            'User Name',
            'User Email',
            'Activity Title',
            'Activity Title (AR)',
            'Type',
            'Is Lead',
            'Invited',
            'Attended',
            'COP',
            'Created At'
        ]);

        // Add data rows
        foreach ($activityUsers as $item) {
            fputcsv($handle, [
                $item->external_id,
                $item->user ? $item->user->first_name . ' ' . $item->user->last_name : '',
                $item->user ? $item->user->email : '',
                $item->activity ? $item->activity->activity_title_en : '',
                $item->activity ? $item->activity->activity_title_ar : '',
                $item->type,
                $item->is_lead ? 'Yes' : 'No',
                $item->invited ? 'Yes' : 'No',
                $item->attended ? 'Yes' : 'No',
                $item->cop ? $item->cop->cop_name : '',
                $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        fclose($handle);
        exit;
    }

    /**
     * Export a single activity user to CSV.
     */
    public function exportSingle($id)
    {
        $activityUser = ActivityUser::with(['user', 'activity', 'cop'])
            ->findOrFail($id);

        $filename = 'activity-user-' . $activityUser->external_id . '-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add headers
        fputcsv($handle, [
            'Field',
            'Value'
        ]);

        // Add data rows
        fputcsv($handle, ['External ID', $activityUser->external_id]);
        fputcsv($handle, ['User', $activityUser->user ? $activityUser->user->first_name . ' ' . $activityUser->user->last_name : '']);
        fputcsv($handle, ['User Email', $activityUser->user ? $activityUser->user->email : '']);
        fputcsv($handle, ['Activity', $activityUser->activity ? $activityUser->activity->activity_title_en : '']);
        fputcsv($handle, ['Activity (AR)', $activityUser->activity ? $activityUser->activity->activity_title_ar : '']);
        fputcsv($handle, ['Type', $activityUser->type]);
        fputcsv($handle, ['Is Lead', $activityUser->is_lead ? 'Yes' : 'No']);
        fputcsv($handle, ['Invited', $activityUser->invited ? 'Yes' : 'No']);
        fputcsv($handle, ['Attended', $activityUser->attended ? 'Yes' : 'No']);
        fputcsv($handle, ['COP', $activityUser->cop ? $activityUser->cop->cop_name : '']);
        fputcsv($handle, ['Created At', $activityUser->created_at ? $activityUser->created_at->format('Y-m-d H:i:s') : '']);
        fputcsv($handle, ['Updated At', $activityUser->updated_at ? $activityUser->updated_at->format('Y-m-d H:i:s') : '']);

        fclose($handle);
        exit;
    }

    /**
     * Get users by activity for AJAX requests.
     */
    public function getUsersByActivity($activityId)
    {
        $activityUsers = ActivityUser::with('user')
            ->where('activity_id', $activityId)
            ->get();

        return response()->json($activityUsers);
    }

    /**
     * Get activities by user for AJAX requests.
     */
    public function getActivitiesByUser($userId)
    {
        $activityUsers = ActivityUser::with('activity')
            ->where('user_id', $userId)
            ->get();

        return response()->json($activityUsers);
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
}