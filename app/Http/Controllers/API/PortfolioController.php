<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Portfolio Management",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/portfolios",
     *     summary="Get all portfolios or a specific portfolio by ID",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional portfolio UUID to fetch a specific portfolio",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Portfolios retrieved successfully"),
     *     @OA\Response(response=404, description="Portfolio not found")
     * )
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('id')) {
                $portfolio = Portfolio::where('portfolio_id', $request->id)->first();
                if (!$portfolio) {
                    return response()->json(['message' => 'Portfolio not found'], 404);
                }
                return response()->json($portfolio);
            }

            $portfolios = Portfolio::all();
            return response()->json([
                'data' => $portfolios,
                'message' => 'Portfolios retrieved successfully'
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
     *     path="/api/portfolios",
     *     summary="Create a new portfolio",
     *     tags={"Portfolios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type"},
     *             @OA\Property(property="name", type="string", example="Leadership Portfolio"),
     *             @OA\Property(property="type", type="string", example="Programmatic"),
     *             @OA\Property(property="description", type="string", example="Portfolio description here"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-31")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Portfolio created successfully"),
     *     @OA\Response(response=400, description="Invalid input data")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:100',
                'description' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $portfolio = Portfolio::create([
                'portfolio_id' => Str::uuid(),
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);

            return response()->json([
                'data' => $portfolio,
                'message' => 'Portfolio created successfully'
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
     *     path="/api/portfolios/{id}",
     *     summary="Update an existing portfolio",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio UUID to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Portfolio Name"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="type", type="string", example="Updated Type")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Portfolio updated successfully"),
     *     @OA\Response(response=404, description="Portfolio not found")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $portfolio = Portfolio::where('portfolio_id', $id)->first();
            if (!$portfolio) {
                return response()->json(['message' => 'Portfolio not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'type' => 'sometimes|string|max:100',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $portfolio->update($validated);

            return response()->json([
                'data' => $portfolio,
                'message' => 'Portfolio updated successfully'
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
     *     path="/api/portfolios/{id}",
     *     summary="Delete a portfolio by UUID",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio UUID to delete",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Portfolio deleted successfully"),
     *     @OA\Response(response=404, description="Portfolio not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $portfolio = Portfolio::where('portfolio_id', $id)->first();

            if (!$portfolio) {
                return response()->json([
                    'message' => 'Portfolio not found',
                    'id_sent' => $id,
                    'all_ids' => Portfolio::pluck('portfolio_id')
                ], 404);
            }

            $portfolio->delete();

            return response()->json(['message' => 'Portfolio deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
