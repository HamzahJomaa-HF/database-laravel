<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectPortfolio;
use Illuminate\Http\Request;

class ProjectPortfolioController extends Controller
{
   
    public function index()
    {
        $relationships = ProjectPortfolio::with(['project', 'portfolio'])->get();
        
        return response()->json([
            'data' => $relationships,
            'message' => 'Project-portfolio relationships retrieved successfully'
        ]);
    }

    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|uuid|exists:projects,project_id',
                'portfolio_id' => 'required|uuid|exists:portfolios,portfolio_id',
                'order' => 'sometimes|integer|min:0',
                'metadata' => 'sometimes|array'
            ]);

            // Check if relationship already exists
            $existing = ProjectPortfolio::where('project_id', $validated['project_id'])
                ->where('portfolio_id', $validated['portfolio_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Project is already attached to this portfolio'
                ], 409);
            }

            $relationship = ProjectPortfolio::create($validated);

            return response()->json([
                'data' => $relationship->load(['project', 'portfolio']),
                'message' => 'Project attached to portfolio successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $relationship = ProjectPortfolio::findOrFail($id);

            $validated = $request->validate([
                'order' => 'sometimes|integer|min:0',
                'metadata' => 'sometimes|array'
            ]);

            $relationship->update($validated);

            return response()->json([
                'data' => $relationship->load(['project', 'portfolio']),
                'message' => 'Project-portfolio relationship updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Relationship not found'
            ], 404);
        }
    }

    
    public function destroy($id)
    {
        try {
            $relationship = ProjectPortfolio::findOrFail($id);
            $relationship->delete();

            return response()->json([
                'message' => 'Project detached from portfolio successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Relationship not found'
            ], 404);
        }
    }

    
    public function getProjectsByPortfolio($portfolioId)
    {
        $projects = ProjectPortfolio::with('project')
            ->where('portfolio_id', $portfolioId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $projects,
            'message' => 'Projects retrieved for portfolio'
        ]);
    }

    
    public function getPortfoliosByProject($projectId)
    {
        $portfolios = ProjectPortfolio::with('portfolio')
            ->where('project_id', $projectId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $portfolios,
            'message' => 'Portfolios retrieved for project'
        ]);
    }
}