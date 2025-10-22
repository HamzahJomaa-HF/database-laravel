<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="Get all activities or a specific activity by ID",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional activity ID to fetch a specific activity",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Activities retrieved successfully"),
     *     @OA\Response(response=404, description="Activity not found")
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('id')) {
            $activity = Activity::find($request->id);
            if (!$activity) {
                return response()->json(['message' => 'Activity not found'], 404);
            }
            return response()->json($activity);
        }

        $activities = Activity::all();
        return response()->json([
            'data' => $activities,
            'message' => 'Activities retrieved successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/activities",
     *     summary="Create a new activity",
     *     tags={"Activities"}
     * )
     */
    public function store(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'activity_title' => 'required|string|max:255',
            'activity_type' => 'required|string|max:255',
            'content_network' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'parent_activity' => 'nullable|uuid|exists:activities,activity_id',
            'target_cop' => 'nullable|uuid|exists:users,id',
        ]);

        // Create activity — model handles activity_id and external_id
        $activity = Activity::create($validated);

        return response()->json([
            'data' => $activity,
            'message' => 'Activity created successfully'
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * @OA\Put(
     *     path="/api/activities/{id}",
     *     summary="Update an existing activity",
     *     tags={"Activities"}
     * )
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $validated = $request->validate([
            'activity_title' => 'sometimes|string|max:255',
            'activity_type' => 'sometimes|string|max:255',
            'content_network' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'parent_activity' => 'nullable|uuid|exists:activities,activity_id',
            'target_cop' => 'nullable|uuid|exists:users,id', // change table/column if needed
        ]);

        $activity->update($validated);

        return response()->json([
            'data' => $activity,
            'message' => 'Activity updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/activities/{id}",
     *     summary="Delete an activity by ID",
     *     tags={"Activities"}
     * )
     */
    public function destroy($id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $activity->delete();

        return response()->json(['message' => 'Activity deleted successfully']);
    }
}
