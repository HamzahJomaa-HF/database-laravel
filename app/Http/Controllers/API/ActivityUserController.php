<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ActivityUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityUserController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Activity Users Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/activity-users",
     *     summary="Get all activity users or a specific activity user by ID",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional activity_user UUID to fetch a specific record",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Activity users retrieved successfully"),
     *     @OA\Response(response=404, description="Activity user not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $activityUser = ActivityUser::where('activity_user_id', $request->id)->first();
                if (!$activityUser) {
                    return response()->json(['message' => 'Activity user not found'], 404);
                }
                return response()->json($activityUser);
            }

            $activityUsers = ActivityUser::all();
            return response()->json([
                'data' => $activityUsers,
                'message' => 'Activity users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/activity-users",
     *     summary="Create a new activity user",
     *     tags={"ActivityUsers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","activity_id"},
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="activity_id", type="string", format="uuid"),
     *             @OA\Property(property="cop_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="is_lead", type="boolean", example=false),
     *             @OA\Property(property="invited", type="boolean", example=false),
     *             @OA\Property(property="attended", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Activity user created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|uuid|exists:users,user_id',
                'activity_id' => 'required|uuid|exists:activities,activity_id',
                'cop_id' => 'nullable|uuid|exists:cops,cop_id',
                'is_lead' => 'sometimes|boolean',
                'invited' => 'sometimes|boolean',
                'attended' => 'sometimes|boolean',
            ]);

            $activityUser = ActivityUser::create(array_merge($validated, [
                'activity_user_id' => (string) Str::uuid(),
            ]));

            return response()->json([
                'data' => $activityUser,
                'message' => 'Activity user created successfully'
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
     *     path="/api/activity-users/{id}",
     *     summary="Update an existing activity user",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity user UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string", format="uuid"),
     *             @OA\Property(property="activity_id", type="string", format="uuid"),
     *             @OA\Property(property="cop_id", type="string", format="uuid", nullable=true),
     *             @OA\Property(property="is_lead", type="boolean"),
     *             @OA\Property(property="invited", type="boolean"),
     *             @OA\Property(property="attended", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Activity user updated successfully"),
     *     @OA\Response(response=404, description="Activity user not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $activityUser = ActivityUser::where('activity_user_id', $id)->first();
            if (!$activityUser) {
                return response()->json(['message' => 'Activity user not found'], 404);
            }

            $validated = $request->validate([
                'user_id' => 'sometimes|uuid|exists:users,user_id',
                'activity_id' => 'sometimes|uuid|exists:activities,activity_id',
                'cop_id' => 'nullable|uuid|exists:cops,cop_id',
                'is_lead' => 'sometimes|boolean',
                'invited' => 'sometimes|boolean',
                'attended' => 'sometimes|boolean',
            ]);

            $activityUser->update($validated);

            return response()->json([
                'data' => $activityUser,
                'message' => 'Activity user updated successfully'
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
     *     path="/api/activity-users/{id}",
     *     summary="Delete an activity user by UUID",
     *     tags={"ActivityUsers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity user UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Activity user deleted successfully"),
     *     @OA\Response(response=404, description="Activity user not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $activityUser = ActivityUser::where('activity_user_id', $id)->first();
            if (!$activityUser) {
                return response()->json(['message' => 'Activity user not found'], 404);
            }

            $activityUser->delete();

            return response()->json(['message' => 'Activity user deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }





    
}
