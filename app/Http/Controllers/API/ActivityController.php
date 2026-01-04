<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Throwable;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Hariri Foundation API",
 *      description="API documentation for Activities Management",
 *      @OA\Contact(email="support@haririfoundation.com")
 * )
 */

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
     *         description="Optional activity UUID to fetch a specific activity",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activities retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Activity")),
     *             @OA\Property(property="message", type="string", example="Activities retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Activity not found"),
     *     @OA\Response(response=500, description="Server error")
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
                return response()->json([
                    'data' => $activity,
                    'message' => 'Activity retrieved successfully'
                ]);
            }

            $activities = Activity::all();
            return response()->json([
                'data' => $activities,
                'message' => 'Activities retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}",
     *     summary="Get a specific activity by ID",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Activity UUID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Activity retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Activity not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function show($id)
    {
        try {
            $activity = Activity::where('activity_id', $id)->first();
            
            if (!$activity) {
                return response()->json(['message' => 'Activity not found'], 404);
            }
            
            return response()->json([
                'data' => $activity,
                'message' => 'Activity retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/activities",
     *     summary="Create a new activity",
     *     tags={"Activities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"activity_title_en", "activity_type"},
     *             @OA\Property(property="external_id", type="string", example="EXT-12345"),
     *             @OA\Property(property="folder_name", type="string", example="Workshop_Folder"),
     *             @OA\Property(property="activity_title_en", type="string", example="English Workshop Title"),
     *             @OA\Property(property="activity_title_ar", type="string", example="عنوان الورشة بالعربية"),
     *             @OA\Property(property="activity_type", type="string", example="Workshop"),
     *             @OA\Property(property="content_network", type="string", example="Online"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-05"),
     *             @OA\Property(property="parent_activity", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="target_cop", type="string", format="uuid", example="987e6543-b21d-43c1-a654-123456789abc"),
     *             @OA\Property(
     *                 property="operational_support", 
     *                 type="array", 
     *                 @OA\Items(type="string", enum={"logistics", "catering", "transportation", "accommodation", "printing", "equipment", "media"}),
     *                 example={"logistics", "catering"}
     *             ),
     *             @OA\Property(property="venue", type="string", example="Conference Hall A"),
     *             @OA\Property(
     *                 property="portfolio_ids", 
     *                 type="array", 
     *                 @OA\Items(type="integer", example=1),
     *                 example={1, 2}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Activity created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Activity created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $allowedSupports = config('operational_support', []);

        try {
            // Add debug logging
            Log::info('Store Activity Request Received:', [
                'request_data' => $request->all(),
                'content_type' => $request->header('Content-Type'),
                'raw_input' => file_get_contents('php://input')
            ]);

            // FIXED: Changed 'nullable' to 'required' for essential fields
          $validated = $request->validate([
    'external_id'         => ['nullable', 'string', 'max:255'],
    'folder_name'         => ['nullable', 'string', 'max:255'],
    'activity_title_en'   => ['required', 'string', 'max:255'],  // Changed to required
    'activity_title_ar'   => ['nullable', 'string', 'max:255'],
    'activity_type'       => ['required', 'string', 'max:255'],  // Changed to required
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

            Log::info('Validated Data:', $validated);

            // Filter operational_support values to only include allowed ones
            if ($request->has('operational_support') && !empty($allowedSupports)) {
                $validated['operational_support'] = collect($request->input('operational_support', []))
                    ->filter(function ($value) use ($allowedSupports) {
                        return in_array($value, $allowedSupports);
                    })
                    ->values()
                    ->toArray();
            }

            // Check if operational_support should be JSON encoded
            if (isset($validated['operational_support']) && is_array($validated['operational_support'])) {
                $validated['operational_support'] = json_encode($validated['operational_support']);
            }

            // 2️⃣ Transaction
            $activity = DB::transaction(function () use ($validated) {
                $portfolioIds = $validated['portfolio_ids'] ?? [];
                unset($validated['portfolio_ids']);

                Log::info('Creating activity with data:', $validated);

                // Create the activity
                $activity = Activity::create($validated);

                Log::info('Activity created:', [
                    'id' => $activity->activity_id,
                    'attributes' => $activity->getAttributes()
                ]);

                // Verify in database
                $dbRecord = DB::table('activities')
                    ->where('activity_id', $activity->activity_id)
                    ->first();
                Log::info('Database record:', (array)$dbRecord);

                if (!empty($portfolioIds)) {
                    $activity->portfolios()->attach($portfolioIds); // Changed from syncWithoutDetaching
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
            Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Throwable $e) {
            // ❌ Any other error (DB, logic, etc.)
            Log::error('Store activity error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            report($e);

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
     *         @OA\Schema(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="external_id", type="string", example="EXT-12345"),
     *             @OA\Property(property="folder_name", type="string", example="Updated_Folder_Name"),
     *             @OA\Property(property="activity_title_en", type="string", example="Updated Workshop Title"),
     *             @OA\Property(property="activity_title_ar", type="string", example="عنوان الورشة المحدث"),
     *             @OA\Property(property="activity_type", type="string", example="Seminar"),
     *             @OA\Property(property="content_network", type="string", example="Offline"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-10-21"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-10-25"),
     *             @OA\Property(property="parent_activity", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="target_cop", type="string", format="uuid", example="987e6543-b21d-43c1-a654-123456789abc"),
     *             @OA\Property(
     *                 property="operational_support", 
     *                 type="array", 
     *                 @OA\Items(type="string", enum={"logistics", "catering", "transportation", "accommodation", "printing", "equipment", "media"}),
     *                 example={"logistics", "media"}
     *             ),
     *             @OA\Property(property="venue", type="string", example="Updated Venue Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Activity updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Activity not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        $allowedSupports = config('operational_support', []);
        
        try {
            Log::info('Update Activity Request:', [
                'activity_id' => $id,
                'request_data' => $request->all()
            ]);

            $activity = Activity::where('activity_id', $id)->first();

            if (!$activity) {
                return response()->json(['message' => 'Activity not found'], 404);
            }

            // Use 'sometimes' for partial updates
            $validated = $request->validate([
                'external_id' => 'sometimes|string|max:255',
                'folder_name' => 'sometimes|string|max:255',
                'activity_title_en' => 'sometimes|string|max:255',
                'activity_title_ar' => 'sometimes|string|max:255',
                'activity_type' => 'sometimes|string|max:255',
                'content_network' => 'sometimes|string|max:255',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
                'parent_activity' => 'sometimes|uuid|exists:activities,activity_id',
                'target_cop' => 'sometimes|uuid',
                'operational_support' => 'sometimes|array',
                'venue' => 'sometimes|string|max:255',
                'portfolio_ids' => 'sometimes|array',
                'portfolio_ids.*' => 'integer|exists:portfolios,portfolio_id',
            ]);

            Log::info('Update validated data:', $validated);

            // Filter operational_support values to only include allowed ones
            if ($request->has('operational_support')) {
                if (!empty($allowedSupports)) {
                    $validated['operational_support'] = collect($request->input('operational_support', []))
                        ->filter(function ($value) use ($allowedSupports) {
                            return in_array($value, $allowedSupports);
                        })
                        ->values()
                        ->toArray();
                } else {
                    $validated['operational_support'] = $request->input('operational_support');
                }
                
                // Convert array to JSON string if needed
                if (is_array($validated['operational_support'])) {
                    $validated['operational_support'] = json_encode($validated['operational_support']);
                }
            }

            // Use transaction to ensure data consistency
            DB::transaction(function () use ($activity, $validated) {
                $portfolioIds = $validated['portfolio_ids'] ?? null;
                unset($validated['portfolio_ids']);
                
                Log::info('Updating activity with:', $validated);
                
                // Update with validated data
                $activity->update($validated);
                
                Log::info('Activity updated:', $activity->getAttributes());
                
                // Sync portfolios if provided
                if ($portfolioIds !== null) {
                    $activity->portfolios()->sync($portfolioIds);
                }
            });

            // Reload the activity with relationships
            $activity->refresh();
            $activity->load('parent', 'children', 'portfolios');

            return response()->json([
                'data' => $activity,
                'message' => 'Activity updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Update validation failed:', $e->errors());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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
     *         @OA\Schema(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Activity deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Activity not found"),
     *     @OA\Response(response=500, description="Server error")
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
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}