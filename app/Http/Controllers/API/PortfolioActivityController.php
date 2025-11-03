<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PortfolioActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioActivityController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Portfolio Activities Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/portfolio-activities",
     *     summary="Get all portfolio activities or a specific one by ID",
     *     tags={"Portfolio Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional portfolio activity UUID to fetch a specific one",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Portfolio activities retrieved successfully"),
     *     @OA\Response(response=404, description="Portfolio activity not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $activity = PortfolioActivity::where('portfolio_activity_id', $request->id)->first();
                if (!$activity) {
                    return response()->json(['message' => 'Portfolio activity not found'], 404);
                }
                return response()->json($activity);
            }

            $activities = PortfolioActivity::all();
            return response()->json([
                'data' => $activities,
                'message' => 'Portfolio activities retrieved successfully'
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
     *     path="/api/portfolio-activities",
     *     summary="Create a new portfolio activity",
     *     tags={"Portfolio Activities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"portfolio_id", "activity_id"},
     *             @OA\Property(property="portfolio_id", type="string", format="uuid", example="uuid-of-portfolio"),
     *             @OA\Property(property="activity_id", type="string", format="uuid", example="uuid-of-activity"),
     *             @OA\Property(property="notes", type="string", example="Optional note about the relation")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Portfolio activity created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'portfolio_id' => 'required|uuid|exists:portfolios,portfolio_id',
                'activity_id' => 'required|uuid|exists:activities,activity_id',
                'notes' => 'nullable|string',
            ]);

            $portfolioActivity = PortfolioActivity::create([
                'portfolio_activity_id' => Str::uuid(),
                'portfolio_id' => $validated['portfolio_id'],
                'activity_id' => $validated['activity_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'data' => $portfolioActivity,
                'message' => 'Portfolio activity created successfully'
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
     *     path="/api/portfolio-activities/{id}",
     *     summary="Update an existing portfolio activity",
     *     tags={"Portfolio Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio activity UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="portfolio_id", type="string", format="uuid", example="uuid-of-portfolio"),
     *             @OA\Property(property="activity_id", type="string", format="uuid", example="uuid-of-activity"),
     *             @OA\Property(property="notes", type="string", example="Updated note")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Portfolio activity updated successfully"),
     *     @OA\Response(response=404, description="Portfolio activity not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $portfolioActivity = PortfolioActivity::where('portfolio_activity_id', $id)->first();
            if (!$portfolioActivity) {
                return response()->json(['message' => 'Portfolio activity not found'], 404);
            }

            $validated = $request->validate([
                'portfolio_id' => 'sometimes|uuid|exists:portfolios,portfolio_id',
                'activity_id' => 'sometimes|uuid|exists:activities,activity_id',
                'notes' => 'nullable|string',
            ]);

            $portfolioActivity->update($validated);

            return response()->json([
                'data' => $portfolioActivity,
                'message' => 'Portfolio activity updated successfully'
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
     *     path="/api/portfolio-activities/{id}",
     *     summary="Delete a portfolio activity by UUID",
     *     tags={"Portfolio Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio activity UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Portfolio activity deleted successfully"),
     *     @OA\Response(response=404, description="Portfolio activity not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $portfolioActivity = PortfolioActivity::where('portfolio_activity_id', $id)->first();

            if (!$portfolioActivity) {
                return response()->json([
                    'message' => 'Portfolio activity not found',
                    'id_sent' => $id,
                    'all_ids' => PortfolioActivity::pluck('portfolio_activity_id')
                ], 404);
            }

            $portfolioActivity->delete();

            return response()->json(['message' => 'Portfolio activity deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
