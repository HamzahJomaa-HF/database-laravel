<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ProgramController extends Controller
{
    /**
     * Display a listing of the programs.
     */
    public function index(Request $request)
    {
        $query = Program::with(['parentProgram', 'subPrograms'])
            ->orderBy('created_at', 'desc');

        // Filter by program type if provided
        if ($request->has('program_type') && $request->program_type) {
            $query->where('program_type', $request->program_type);
        }

        // Filter by parent program if provided
        if ($request->has('parent_program_id') && $request->parent_program_id) {
            $query->where('parent_program_id', $request->parent_program_id);
        }

        // Filter by type (Center/Program) if provided
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('folder_name', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%");
            });
        }

        $programs = $query->paginate(20);

        // Get available program types for filter dropdown
        $programTypes = Program::distinct()->pluck('program_type')->filter()->values();
        $parentPrograms = Program::whereNull('parent_program_id')
            ->orWhere('type', 'Center')
            ->get(['program_id', 'name']);

        return view('programs.index', compact('programs', 'programTypes', 'parentPrograms'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function createCenter()
    {
        // Get parent programs for dropdown (Centers and Programs without parent)
        $parentPrograms = Program::where(function($query) {
            $query->where('type', 'Center')
                  ->orWhereNull('parent_program_id');
        })
        ->where('type', '!=', 'Program') 
        ->orderBy('name')
        ->get(['program_id', 'name', 'type', 'program_type']);

        // Get available program types
        $programTypes = ['Center Program', 'Sub-Program', 'Local Program', 'Flagship', 'Center'];

        return view('programs.create.centers', compact('parentPrograms', 'programTypes'));
         // Redirect to index instead of show
        return redirect()->route('programs.index')
            ->with('success', 'Center created successfully!');
   
    }



      public function createFlagshipLocal()
    {
        // Get parent programs for dropdown (Centers only)
        $parentPrograms = Program::where('type', 'Center')
            ->orderBy('name')
            ->get(['program_id', 'name', 'folder_name']);
        
        return view('programs.create.flagshiplocal', compact('parentPrograms'));
         // Redirect to index instead of show
        return redirect()->route('programs.index')
            ->with('success', 'Center created successfully!');
   
    }



       public function createSubprogram()
    {
        // Get parent programs for dropdown (BOTH Programs AND Centers)
    $parentPrograms = Program::where(function($query) {
        // Get Programs with specific program types
        $query->where('type', 'Program')
              ->whereIn('program_type', ['Flagship', 'Local Program', 'Local Program', 'Management']);
    })
    ->orWhere(function($query) {
        // ALSO get Centers (type = 'Center', program_type = 'Center')
        $query->where('type', 'Center')
              ->where('program_type', 'Center');
    })
    ->orderBy('name')
    ->get(['program_id', 'name', 'folder_name', 'type', 'program_type']);
        return view('programs.create.subprogram', compact('parentPrograms'));

        // Redirect to index instead of show
        return redirect()->route('programs.index')
            ->with('success', 'Center created successfully!');
   
    }
       public function storeCenter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => 'nullable|string|max:100|unique:programs,folder_name',
            'description' => 'nullable|string',
        ]);
        
        $validated['type'] = 'Center';
        $validated['program_type'] = 'Center';
        $validated['parent_program_id'] = null;
        
        try {
            DB::beginTransaction();
            $program = Program::create($validated);
            DB::commit();
            
            return redirect()->route('programs.index')
                ->with('success', 'Center created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create center: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Store a newly created Flagship or Local Program.
     */
    public function storeFlagshipLocal(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => 'nullable|string|max:100|unique:programs,folder_name',
            'program_type' => 'required|in:Flagship,Local Program',
            'description' => 'nullable|string',
        ]);
        
        $validated['type'] = 'Program';
        
        try {
            DB::beginTransaction();
            $program = Program::create($validated);
            DB::commit();
            
            return redirect()->route('programs.index')
                ->with('success', 'Program created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create program: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Store a newly created program in storage.
     */
    public function storeSubprogram(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'folder_name' => 'nullable|string|max:100|unique:programs,folder_name',
        'program_type_select' => 'required|in:Center,Flagship,Local Program,Local Program,Management',
        'description' => 'nullable|string',
        'parent_program_id' => 'required|exists:programs,program_id',
    ]);
    
    // Set default values
    $validated['type'] = 'Program';
    
    // Determine program_type based on selected parent type
    if ($validated['program_type_select'] === 'Center') {
        $validated['program_type'] = 'Center Program'; // Child of Center = Center Program
    } else {
        $validated['program_type'] = 'Sub-Program'; // Child of Flagship/Local Program = Sub-Program
    }
    
    // Validate parent program matches selected program type
    $parent = Program::find($validated['parent_program_id']);
    if (!$parent || $parent->program_type !== $validated['program_type_select']) {
        return back()->withErrors(['parent_program_id' => 'Selected parent does not match the program type'])->withInput();
    }
    
    // Additional validation for program type consistency
    if ($validated['program_type'] === 'Center Program') {
        // Center Program must have a Center as parent
        if ($parent->type !== 'Center' || $parent->program_type !== 'Center') {
            return back()->withErrors(['parent_program_id' => 'Center Programs must have a Center as parent'])->withInput();
        }
    }
    
    if ($validated['program_type'] === 'Sub-Program') {
        // Sub-Program must have a Program as parent (not a Center)
        if ($parent->type !== 'Program') {
            return back()->withErrors(['parent_program_id' => 'Sub-Programs must have a Program as parent'])->withInput();
        }
    }
    
    // Remove program_type_select as it's not a database field
    unset($validated['program_type_select']);
    
     try {
            DB::beginTransaction();
            $program = Program::create($validated);
            DB::commit();
            
            return redirect()->route('programs.index')
                ->with('success', 'Program created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create program: ' . $e->getMessage())->withInput();
        }

}











    public function editCenter($id)
    {
        $program = Program::findOrFail($id);
        
        // Ensure this is a Center
        if ($program->type !== 'Center' || $program->program_type !== 'Center') {
            abort(404, 'Not a valid Center');
        }

        // For Centers, we don't need parent programs dropdown as Centers have no parent
        return view('programs.edit.center', compact('program'));
    }

    /**
     * Update a Center in storage.
     */
    public function updateCenter(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        
        // Ensure this is a Center
        if ($program->type !== 'Center' || $program->program_type !== 'Center') {
            abort(404, 'Not a valid Center');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('programs', 'folder_name')->ignore($program->program_id, 'program_id')
            ],
            'description' => 'nullable|string',
        ]);

        // Center-specific fields (should not be changed for Centers)
        $validated['type'] = 'Center';
        $validated['program_type'] = 'Center';
        $validated['parent_program_id'] = null;

        try {
            DB::beginTransaction();
            $program->update($validated);
            DB::commit();
            
            return redirect()->route('programs.index')
                ->with('success', 'Center updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update center: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing a Flagship or Local Program.
     */
    public function editFlagshipLocal($id)
    {
        $program = Program::findOrFail($id);
        
        // Ensure this is a Program (not Center) with appropriate program_type
        if ($program->type !== 'Program' || 
            !in_array($program->program_type, ['Flagship', 'Local Program'])) {
            abort(404, 'Not a valid Flagship or Local Program');
        }

        // Get parent programs for dropdown (Centers only for Flagship/Local Programs)
        $parentPrograms = Program::where('type', 'Center')
            ->where('program_id', '!=', $program->program_id) // Exclude self
            ->orderBy('name')
            ->get(['program_id', 'name', 'folder_name']);

        return view('programs.edit.flagshiplocal', compact('program', 'parentPrograms'));
    }

    /**
     * Update a Flagship or Local Program in storage.
     */
   public function updateFlagshipLocal(Request $request, $id)
{
    $program = Program::findOrFail($id);
    
    // Ensure this is a Program (not Center) with appropriate program_type
    if ($program->type !== 'Program' || 
        !in_array($program->program_type, ['Flagship', 'Local Program'])) {
        abort(404, 'Not a valid Flagship or Local Program');
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'folder_name' => [
            'nullable',
            'string',
            'max:100',
            Rule::unique('programs', 'folder_name')->ignore($program->program_id, 'program_id')
        ],
        'program_type' => 'required|in:Flagship,Local Program',
        'description' => 'nullable|string',
        'parent_program_id' => [
            'nullable',
            Rule::exists('programs', 'program_id')->where(function ($query) use ($program) {
                $query->where('program_id', '!=', $program->program_id)
                      ->where('type', 'Center'); // Parent must be a Center
            }),
        ],
    ]);

    // Program-specific field
    $validated['type'] = 'Program';

    // Additional validation - FIX: Check if key exists first
    if (isset($validated['parent_program_id']) && $validated['parent_program_id'] && 
        $this->isCircularReference($program, $validated['parent_program_id'])) {
        return back()->withErrors(['parent_program_id' => 'Cannot set parent as it would create a circular reference'])->withInput();
    }

    try {
        DB::beginTransaction();
        $program->update($validated);
        DB::commit();
        
        return redirect()->route('programs.index')
            ->with('success', 'Program updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to update program: ' . $e->getMessage())->withInput();
    }
}


    /**
     * Show the form for editing a Sub-Program.
     */
public function editSubprogram($id)
{
    $program = Program::with('parentProgram')->findOrFail($id); // Use 'parentProgram'
    
    // Ensure this is a Sub-Program or Center Program
    if ($program->type !== 'Program' || 
        !in_array($program->program_type, ['Sub-Program', 'Center Program'])) {
        abort(404, 'Not a valid Sub-Program or Center Program');
    }

    // Get parent programs for dropdown based on program type
    if ($program->program_type === 'Center Program') {
        // Center Programs can only have Centers as parents
        $parentPrograms = Program::where('type', 'Center')
            ->where('program_type', 'Center')
            ->where('program_id', '!=', $program->program_id)
            ->orderBy('name')
            ->get(['program_id', 'name', 'folder_name', 'type', 'program_type']);
    } else {
        // Sub-Programs can have Programs as parents OR Centers
        $parentPrograms = Program::where(function($query) use ($program) {
            // Programs with specific program types
            $query->where('type', 'Program')
                  ->whereIn('program_type', ['Flagship', 'Local Program', 'Local Program', 'Management'])
                  ->where('program_id', '!=', $program->program_id);
        })
        ->orWhere(function($query) use ($program) {
            // ALSO get Centers (type = 'Center', program_type = 'Center')
            $query->where('type', 'Center')
                  ->where('program_type', 'Center')
                  ->where('program_id', '!=', $program->program_id);
        })
        ->orderBy('name')
        ->get(['program_id', 'name', 'folder_name', 'type', 'program_type']);
    }

    return view('programs.edit.subprogram', compact('program', 'parentPrograms'));
}
    /**
     * Update a Sub-Program in storage.
     */
    public function updateSubprogram(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        
        // Ensure this is a Sub-Program or Center Program
        if ($program->type !== 'Program' || 
            !in_array($program->program_type, ['Sub-Program', 'Center Program'])) {
            abort(404, 'Not a valid Sub-Program or Center Program');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('programs', 'folder_name')->ignore($program->program_id, 'program_id')
            ],
            'program_type_select' => 'required|in:Center,Flagship,Local Program,Local Program,Management',
            'description' => 'nullable|string',
            'parent_program_id' => [
                'required',
                Rule::exists('programs', 'program_id')->where(function ($query) use ($program) {
                    $query->where('program_id', '!=', $program->program_id);
                }),
            ],
        ]);

        // Set default values
        $validated['type'] = 'Program';
        
        // Determine program_type based on selected parent type
        if ($validated['program_type_select'] === 'Center') {
            $validated['program_type'] = 'Center Program'; // Child of Center = Center Program
        } else {
            $validated['program_type'] = 'Sub-Program'; // Child of Flagship/Local Program = Sub-Program
        }
        
        // Validate parent program matches selected program type
        $parent = Program::find($validated['parent_program_id']);
        if (!$parent || $parent->program_type !== $validated['program_type_select']) {
            return back()->withErrors(['parent_program_id' => 'Selected parent does not match the program type'])->withInput();
        }
        
        // Additional validation for program type consistency
        if ($validated['program_type'] === 'Center Program') {
            // Center Program must have a Center as parent
            if ($parent->type !== 'Center' || $parent->program_type !== 'Center') {
                return back()->withErrors(['parent_program_id' => 'Center Programs must have a Center as parent'])->withInput();
            }
        }
        
        if ($validated['program_type'] === 'Sub-Program') {
            // Sub-Program must have a Program as parent (not a Center)
            if ($parent->type !== 'Program') {
                return back()->withErrors(['parent_program_id' => 'Sub-Programs must have a Program as parent'])->withInput();
            }
        }
        
        // Remove program_type_select as it's not a database field
        unset($validated['program_type_select']);
        
        // Check for circular reference
        if ($validated['parent_program_id'] && $this->isCircularReference($program, $validated['parent_program_id'])) {
            return back()->withErrors(['parent_program_id' => 'Cannot set parent as it would create a circular reference'])->withInput();
        }

        try {
            DB::beginTransaction();
            $program->update($validated);
            DB::commit();
            
            return redirect()->route('programs.index')
                ->with('success', 'Program updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update program: ' . $e->getMessage())->withInput();
        }
    }


























    /**
     * Remove the specified program from storage.
     */
    public function destroy($id)
    {
        $program = Program::with(['subPrograms', 'projects'])->findOrFail($id);

        // Check if program has sub-programs
        if ($program->subPrograms->count() > 0) {
            return back()->with('error', 'Cannot delete program because it has sub-programs. Please delete them first.');
        }

        // Check if program has projects
        if ($program->projects->count() > 0) {
            return back()->with('error', 'Cannot delete program because it has associated projects. Please reassign or delete them first.');
        }

        try {
            DB::beginTransaction();

            $program->delete();

            DB::commit();

            return redirect()->route('programs.index')
                ->with('success', 'Program deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified program.
     */
    public function softDelete($id)
    {
        $program = Program::findOrFail($id);

        try {
            $program->delete();
            return back()->with('success', 'Program moved to trash successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }
/**
 * Check for circular reference in parent-child hierarchy.
 */
private function isCircularReference(Program $program, $parentId)
{
    $currentParentId = $parentId;
    
    while ($currentParentId) {
        if ($currentParentId == $program->program_id) {
            return true;
        }
        
        $parent = Program::find($currentParentId);
        if (!$parent || !$parent->parent_program_id) {
            break;
        }
        
        $currentParentId = $parent->parent_program_id;
    }
    
    return false;
}
    /**
     * Restore a soft-deleted program.
     */
    public function restore($id)
    {
        $program = Program::withTrashed()->findOrFail($id);

        try {
            $program->restore();
            return back
            
            ()->with('success', 'Program restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore program: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a program.
     */
    public function forceDelete($id)
    {
        $program = Program::withTrashed()->findOrFail($id);

        try {
            DB::beginTransaction();

            // Check for existing relationships
            if ($program->subPrograms()->withTrashed()->count() > 0 ||
                $program->projects()->withTrashed()->count() > 0) {
                return back()->with('error', 'Cannot permanently delete program because it has related records.');
            }

            $program->forceDelete();

            DB::commit();

            return redirect()->route('programs.trash')
                ->with('success', 'Program permanently deleted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }

    /**
     * Display a list of soft-deleted programs.
     */
    public function trash()
    {
        $deletedPrograms = Program::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('programs.trash', compact('deletedPrograms'));
    }

    /**
     * Check for circular reference in parent-child hierarchy.
     */
   

  
   

}