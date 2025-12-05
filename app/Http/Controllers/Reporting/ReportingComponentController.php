<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpComponent; // Assuming you have this model

class ReportingComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $components = RpComponent::all();
        return view('reporting.components.index', compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reporting.components.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_components,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'external_id' => 'nullable|uuid'
        ]);

        RpComponent::create($validated);

        return redirect()->route('reporting.components.index')
            ->with('success', 'Component created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RpComponent $rpComponent)
    {
        return view('reporting.components.show', compact('rpComponent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RpComponent $rpComponent)
    {
        return view('reporting.components.edit', compact('rpComponent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RpComponent $rpComponent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:rp_components,code,' . $rpComponent->rp_components_id . ',rp_components_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $rpComponent->update($validated);

        return redirect()->route('reporting.components.index')
            ->with('success', 'Component updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RpComponent $rpComponent)
    {
        $rpComponent->delete();

        return redirect()->route('reporting.components.index')
            ->with('success', 'Component deleted successfully.');
    }
}