<?php

namespace App\Http\Controllers;

use App\Models\Cop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CopController extends Controller
{
    /**
     * Show the form for creating a new COP.
     */
    public function create()
    {
        return view('cops.create');
    }

    /**
     * Store a newly created COP.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'cop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'external_id' => 'nullable|string|max:255|unique:cops,external_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $cop = Cop::create($request->only([
                'cop_name', 
                'description', 
                'external_id'
            ]));

            return redirect()->route('cops.index')
                ->with('success', 'COP created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create COP: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified COP.
     */
    public function edit($id)
    {
        $cop = Cop::find($id);

        if (!$cop) {
            abort(404, 'COP not found');
        }

        return view('cops.edit', compact('cop')); 
    }

    /**
     * Update the specified COP.
     */
    public function update(Request $request, $id)
    {
        // Find the COP
        $cop = Cop::find($id);

        if (!$cop) {
            abort(404, 'COP not found');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'cop_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'external_id' => 'nullable|string|max:255|unique:cops,external_id,' . $cop->cop_id . ',cop_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update the COP
            $cop->update($request->only([
                'cop_name', 
                'description', 
                'external_id'
            ]));

            return redirect()->route('cops.index')
                ->with('success', 'COP updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update COP: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified COP.
     */
    public function destroy($id)
    {
        // Find the COP
        $cop = Cop::find($id);

        if (!$cop) {
            abort(404, 'COP not found');
        }

        try {
            // Use soft delete
            $cop->delete();

            return redirect()->route('cops.index')
                ->with('success', 'COP deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete COP: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of COPs.
     */
    public function index()
    {
        $cops = Cop::latest()->paginate(10);
        return view('cops.index', compact('cops'));
    }
}