<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Hariri Foundation project - Activities Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="Get all activities or a specific activity by ID",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional activity UUID to fetch a specific activity",
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
            $activity = Activity::where('activity_id', $request->id)->first();
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
     *     tags={"Activities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"activity_title", "activity_type", "content_network", "start_date", "end_date"},
     *             @OA\Property(property="activity_title", type="string", example="Workshop on AI"),
     *             @OA\Property(property="activity_type", type="string", example="Training"),
     *             @OA\Property(property="content_network", type="string", example="Online"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-22"),
     *             @OA\Property(property="parent_activity", type="string", format="uuid", example="a1b2c3d4-5678-90ab-cdef-1234567890ab"),
     *             @OA\Property(property="target_cop", type="string", format="uuid", example="b2c3d4e5-6789-0abc-def1-234567890bcd")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Activity created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'activity_title' => 'required|string|max:255',
                'activity_type' => 'required|string|max:255',
                'content_network' => 'nullable|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'parent_activity' => 'nullable|uuid|exists:activities,activity_id',
                'target_cop' => 'nullable|uuid|exists:users,id',
            ]);

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
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="activity_title", type="string", example="Updated Workshop Title"),
     *             @OA\Property(property="activity_type", type="string", example="Seminar"),
     *             @OA\Property(property="content_network", type="string", example="Offline"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-21"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-25"),
     *             @OA\Property(property="parent_activity", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="target_cop", type="string", format="uuid", example="987e6543-b21d-43c1-a654-123456789abc")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Activity updated successfully"),
     *     @OA\Response(response=404, description="Activity not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::where('activity_id', $id)->first();

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
            'target_cop' => 'nullable|uuid|exists:users,id',
        ]);

        $activity->fill($validated);

        if (isset($validated['activity_type']) && $validated['activity_type'] !== $activity->getOriginal('activity_type')) {
            $activity->external_id = 'act_' . date('Y_m') . '_' . strtolower($activity->activity_type) . '_' . substr(Str::uuid(), 0, 4);
        }

        $activity->save();

        return response()->json([
            'data' => $activity,
            'message' => 'Activity updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/activities/{id}",
     *     summary="Delete an activity by UUID",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Activity deleted successfully"),
     *     @OA\Response(response=404, description="Activity not found")
     * )
     */
    public function destroy($id)
    {
        $activity = Activity::where('activity_id', $id)->first();

        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $activity->delete();

        return response()->json(['message' => 'Activity deleted successfully']);
    }
}
