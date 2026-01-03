<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

        public function store(Request $request)
    {
        $allowedSupports = config('operational_support');

        try {
            // 1️⃣ Validate request
            $validated = $request->validate([
                'external_id'         => ['nullable', 'string', 'max:255'],
                'folder_name'         => ['nullable', 'string', 'max:255'],
                'activity_title_en'   => ['nullable', 'string', 'max:255'],
                'activity_title_ar'   => ['nullable', 'string', 'max:255'],
                'activity_type'       => ['nullable', 'string', 'max:255'],
                'content_network'     => ['nullable', 'string'],
                'start_date'          => ['nullable', 'date'],
                'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
                'parent_activity'     => ['nullable', 'uuid', 'exists:activities,activity_id'],
                'target_cop'          => ['nullable', 'uuid'],
                'operational_support' => ['nullable', 'array'],
                'venue'               => ['nullable', 'string', 'max:255'],

                'portfolio_ids'       => ['nullable', 'array'],
                'portfolio_ids.*'     => ['integer', 'exists:portfolios,portfolio_id'],
            ]);

            $validated['operational_support'] = collect(
                $request->input('operational_support', [])
            )
            ->only($allowedSupports)                 // ❌ removes "hamza"
            ->map(fn ($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN))
            ->toArray();

            // 2️⃣ Transaction
            $activity = DB::transaction(function () use ($validated) {

                $portfolioIds = $validated['portfolio_ids'] ?? [];
                unset($validated['portfolio_ids']);

                $activity = Activity::create($validated);

                if (!empty($portfolioIds)) {
                    $activity->portfolios()->syncWithoutDetaching($portfolioIds);
                }

                return $activity;
            });

            // 3️⃣ Load relations
            $activity->load('parent', 'children', 'portfolios');

            return response()->json([
                'success' => true,
                'message' => 'Activity created successfully.',
                'data'    => $activity,
            ], 201);

        } catch (ValidationException $e) {

            // ❌ Validation error
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            // ❌ Any other error (DB, logic, etc.)
            report($e); // logs to storage/logs/laravel.log

            return response()->json([
                'success' => false,
                'message' => 'Failed to create activity.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error.',
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
     *             @OA\Property(property="activity_title_en", type="string", example="Updated Workshop Title"),
     *             @OA\Property(property="activity_title_ar", type="string", example="عنوان الورشة المحدث"),
     *             @OA\Property(property="folder_name", type="string", example="Updated_Folder_Name"),
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
        try {
            $activity = Activity::where('activity_id', $id)->first();

            if (!$activity) {
                return response()->json(['message' => 'Activity not found'], 404);
            }

            $validated = $request->validate([
                'activity_title_en' => 'sometimes|required_without:activity_title_ar|string|max:255',
                'activity_title_ar' => 'sometimes|required_without:activity_title_en|string|max:255',
                'folder_name' => 'nullable|string|max:255',
                'activity_type' => 'sometimes|string|max:255',
                'content_network' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'parent_activity' => 'nullable|uuid|exists:activities,activity_id',
                'target_cop' => 'nullable|uuid|exists:users,user_id',

            ]);

            $activity->update($validated);

            return response()->json([
                'data' => $activity,
                'message' => 'Activity updated successfully'
            ]);
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
        try {
            $activity = Activity::where('activity_id', $id)->first();

            if (!$activity) {
                return response()->json(['message' => 'Activity not found'], 404);
            }

            $activity->delete();

            return response()->json(['message' => 'Activity deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
