<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['program', 'parent', 'children'])
            ->orderBy('created_at', 'desc');

      

        // Filter by program if provided
        if ($request->has('program_id') && $request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by parent project if provided
        if ($request->has('parent_project_id') && $request->parent_project_id) {
            $query->where('parent_project_id', $request->parent_project_id);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Search by name, external_id, or folder_name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhere('folder_name', 'like', "%{$search}%");
            });
        }

        $projects = $query->paginate(20);

        // Get filter data
        $programs = Program::orderBy('name')->get(['program_id', 'name']);
        $parentProjects = Project::whereNull('parent_project_id')
            ->orderBy('name')
            ->get(['project_id', 'name']);
        
       

        return view('projects.index', compact(
            'projects', 
            'programs', 
            'parentProjects',
           
        ));
    }

    /**
     * Show the form for creating a new project.
     */
   public function create()
{
    // Debug: Check database connection
    try {
        \DB::connection()->getPdo();
        logger('Database connected successfully');
    } catch (\Exception $e) {
        logger('Database connection failed: ' . $e->getMessage());
    }

    // Get programs for dropdown
    $programs = Program::orderBy('program_type')
        ->whereNotNull('parent_program_id')
        ->orderBy('name')
        ->get(['program_id', 'name', 'program_type', 'type', 'folder_name']);

    // Debug: Log what we're getting
    logger('Programs count: ' . $programs->count());
    logger('Programs data: ' . json_encode($programs->toArray()));

    // Group them
    $programsGrouped = $programs->groupBy('program_type');
    logger('Programs grouped keys: ' . json_encode($programsGrouped->keys()->toArray()));

    // Get parent projects for dropdown
    $parentProjects = Project::whereNull('parent_project_id')
        ->orderBy('name')
        ->get(['project_id', 'name', 'external_id']);

    // Debug parent projects
    logger('Parent projects count: ' . $parentProjects->count());

    // Get available project types and groups
    
    // Pass data to view
    return view('projects.create', compact(
        'programs', 
        'programsGrouped',
        'parentProjects',
        
    ));
}
    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('projects', 'folder_name')
            ],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'program_id' => 'required|exists:programs,program_id',
            'parent_project_id' => [
                'nullable',
                'exists:projects,project_id',
                function ($attribute, $value, $fail) use ($request) {
                    // Check for circular reference
                    if ($value && $this->isCircularReference(null, $value)) {
                        $fail('Cannot set parent as it would create a circular reference.');
                    }
                }
            ],
           
            'external_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('projects', 'external_id')
            ],
        ]);

        try {
            DB::beginTransaction();
            
            $project = Project::create($validated);
            
            DB::commit();
            
            return redirect()->route('projects.index')
                ->with('success', 'Project created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create project: ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Show the form for editing the specified project.
     */
   /**
 * Show the form for editing the specified project.
 */
public function edit($id)
{
    $project = Project::with(['program', 'parent', 'children'])->findOrFail($id);
    
    // Get programs for dropdown - SAME AS CREATE METHOD
    $programs = Program::orderBy('program_type')
        ->whereNotNull('parent_program_id')
        ->orderBy('name')
        ->get(['program_id', 'name', 'program_type', 'type', 'folder_name']);
    
    // Group them - ADD THIS LINE
    $programsGrouped = $programs->groupBy('program_type');
    
    // Get parent projects for dropdown (exclude current project and its children)
    $parentProjects = Project::whereNull('parent_project_id')
        ->where('project_id', '!=', $id)
        ->whereNotIn('project_id', $project->children->pluck('project_id'))
        ->orderBy('name')
        ->get(['project_id', 'name', 'external_id']);
    
    // Get available project types and groups

    return view('projects.edit', compact(
        'project',
        'programs', 
        'programsGrouped', // ADD THIS
        'parentProjects',
        
    ));
}
    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_name' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('projects', 'folder_name')->ignore($project->project_id, 'project_id')
            ],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'program_id' => 'required|exists:programs,program_id',
            'parent_project_id' => [
                'nullable',
                'exists:projects,project_id',
                function ($attribute, $value, $fail) use ($project) {
                    if ($value == $project->project_id) {
                        $fail('A project cannot be its own parent.');
                    }
                    
                    // Check for circular reference
                    if ($value && $this->isCircularReference($project, $value)) {
                        $fail('Cannot set parent as it would create a circular reference.');
                    }
                }
            ],
           
            'external_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('projects', 'external_id')->ignore($project->project_id, 'project_id')
            ],
        ]);

        try {
            DB::beginTransaction();
            
            $project->update($validated);
            
            DB::commit();
            
            return redirect()->route('projects.index')
                ->with('success', 'Project updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update project: ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy($id)
    {
        $project = Project::with(['children'])->findOrFail($id);

        // Check if project has child projects
        if ($project->children->count() > 0) {
            return back()->with('error', 'Cannot delete project because it has child projects. Please delete them first.');
        }

        try {
            DB::beginTransaction();
            
            $project->delete();
            
            DB::commit();
            
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified project.
     */
    public function softDelete($id)
    {
        $project = Project::findOrFail($id);

        try {
            $project->delete();
            return back()->with('success', 'Project moved to trash successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted project.
     */
    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        try {
            $project->restore();
            return back()->with('success', 'Project restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore project: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a project.
     */
    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        // Check for child projects
        if ($project->children()->withTrashed()->count() > 0) {
            return back()->with('error', 'Cannot permanently delete project because it has related child projects.');
        }

        try {
            DB::beginTransaction();
            
            $project->forceDelete();
            
            DB::commit();
            
            return redirect()->route('projects.trash')
                ->with('success', 'Project permanently deleted!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Display a list of soft-deleted projects.
     */
    public function trash()
    {
        $deletedProjects = Project::onlyTrashed()
            ->with(['program', 'parent'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('projects.trash', compact('deletedProjects'));
    }

    /**
     * Check for circular reference in parent-child hierarchy.
     */
    private function isCircularReference(Project $project = null, $parentId)
    {
        // If we're creating a new project ($project is null), check parent's hierarchy
        if ($project === null) {
            return false; // New project can't have circular reference yet
        }

        $currentParentId = $parentId;
        
        while ($currentParentId) {
            // If we find the project itself in the parent chain
            if ($currentParentId == $project->project_id) {
                return true;
            }
            
            $parent = Project::find($currentParentId);
            if (!$parent || !$parent->parent_project_id) {
                break; // Reached the top of the hierarchy
            }
            
            $currentParentId = $parent->parent_project_id; // Move up the chain
        }
        
        return false;
    }

    /**
     * Get projects for a specific program (for AJAX requests)
     */
    public function getByProgram($programId)
    {
        $projects = Project::where('program_id', $programId)
            ->orderBy('name')
            ->get(['project_id', 'name', 'external_id']);
            
        return response()->json($projects);
    }
}