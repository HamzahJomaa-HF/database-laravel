<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    /**
     * Show the form for creating a new portfolio.
     */
    public function create()
    {
        $cops = Cop::orderBy('cop_name')->get();
        return view('portfolios.create', compact('cops'));
    }

    /**
     * Store a newly created portfolio.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255|unique:portfolios,external_id',
            'cops' => 'nullable|array',
            'cops.*' => 'exists:cops,cop_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $portfolio = Portfolio::create($request->only([
                'name', 
                'description', 
                'type', 
                'external_id'
            ]));

            // Create cop associations - Similar to EmployeeController
            if ($request->filled('cops') && is_array($request->cops)) {
                foreach ($request->cops as $copId) {
                    if (!empty($copId)) {
                        \DB::table('cops_portfolios')->insert([
                            'id' => \Illuminate\Support\Str::uuid(),
                            'cop_id' => $copId,
                            'portfolio_id' => $portfolio->portfolio_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            return redirect()->route('portfolios.index', $portfolio->portfolio_id)
                ->with('success', 'Portfolio created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create portfolio: ' . $e->getMessage())
                ->withInput();
        }
    }

   

    /**
     * Show the form for editing the specified portfolio.
     */
    public function edit($id)
    {
        $portfolio = Portfolio::with(['cops'])->find($id);
        $cops = Cop::orderBy('cop_name')->get();

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Get current portfolio's cop IDs from pivot table (multiple cops)
        $currentCopIds = $portfolio->cops()->pluck('cops.cop_id')->toArray();

        return view('portfolios.edit', compact('portfolio', 'cops', 'currentCopIds')); 
    }

    /**
     * Update the specified portfolio.
     */
    public function update(Request $request, $id)
    {
        // Find the portfolio
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255|unique:portfolios,external_id,' . $portfolio->portfolio_id . ',portfolio_id',
            'cops' => 'nullable|array',
            'cops.*' => 'exists:cops,cop_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update the portfolio
            $portfolio->update($request->only([
                'name', 
                'description', 
                'type', 
                'external_id'
            ]));

            // SYNC COP ASSOCIATIONS (Add/Remove multiple cops) - Similar to EmployeeController
            if ($request->has('cops')) {
                // Get current cop IDs
                $currentCopIds = $portfolio->cops()->pluck('cops.cop_id')->toArray();
                $newCopIds = $request->cops;
                
                // Find cops to add
                $copsToAdd = array_diff($newCopIds, $currentCopIds);
                
                // Find cops to remove
                $copsToRemove = array_diff($currentCopIds, $newCopIds);
                
                // Add new cops
                foreach ($copsToAdd as $copId) {
                    \DB::table('cops_portfolios')->insert([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'cop_id' => $copId,
                        'portfolio_id' => $portfolio->portfolio_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Remove old cops
                \DB::table('cops_portfolios')
                    ->where('portfolio_id', $portfolio->portfolio_id)
                    ->whereIn('cop_id', $copsToRemove)
                    ->delete();
            } else {
                // Remove all cops if none selected
                \DB::table('cops_portfolios')
                    ->where('portfolio_id', $portfolio->portfolio_id)
                    ->delete();
            }

            return redirect()->route('portfolios.index', $portfolio->portfolio_id)
                ->with('success', 'Portfolio updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update portfolio: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified portfolio.
     */
    public function destroy($id)
    {
        // Find the portfolio
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        try {
            // Delete cop associations first (similar to EmployeeController)
            \DB::table('cops_portfolios')
                ->where('portfolio_id', $portfolio->portfolio_id)
                ->delete();
                
            // Use soft delete
            $portfolio->delete();

            return redirect()->route('portfolios.index')
                ->with('success', 'Portfolio deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete portfolio: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of portfolios.
     */
    public function index()
    {
        $portfolios = Portfolio::latest()->paginate(10);
        return view('portfolios.index', compact('portfolios'));
    }
}