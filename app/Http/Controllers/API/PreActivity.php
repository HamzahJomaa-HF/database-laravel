<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PreActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = PreActivity::all();
        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Basic Information
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'support' => 'required|array',
            'support.*' => 'string',
            'focal_point_username' => 'required|string|max:255',
            
            // Main Data
            'project' => 'required|string|max:255',
            'partners' => 'nullable|string',
            'location' => 'required|string|max:255',
            'venue_availability' => 'nullable|string|max:255',
            'activity_date' => 'nullable|date',
            'activity_time' => 'nullable|date_format:H:i',
            'activity_date_time' => 'nullable|date',
            'sessions_number' => 'nullable|integer|min:0',
            'activity_type' => 'nullable|string|max:255',
            'participants_number' => 'nullable|integer|min:0',
            
            // Data, Monitoring & Evaluation
            'invitations' => 'nullable|array',
            'attendance_list' => 'nullable|array',
            'mom_list' => 'nullable|array',
            'registration_form' => 'nullable|string|max:255',
            'post_evaluation' => 'nullable|string|max:255',
            'certificate_type' => 'nullable|array',
            
            // Logistics & Procurement
            'sound_system' => 'nullable|array',
            'zoom_type' => 'nullable|string|max:255',
            'setup' => 'nullable|array',
            'setup_type' => 'nullable|string|max:255',
            'screen_type' => 'nullable|array',
            'hardware' => 'nullable|array',
            'wifi' => 'nullable|array',
            'rollups_needed' => 'nullable|string|max:255',
            'flags_needed' => 'nullable|string|max:255',
            'podium' => 'nullable|string|max:255',
            'food' => 'nullable|array',
            'note_papers' => 'nullable|string|max:255',
            'staff_names' => 'nullable|string',
            'volunteer_numbers' => 'nullable|integer|min:0',
            'support_areas' => 'nullable|string',
            
            // Communications
            'coverage' => 'nullable|array',
            'media' => 'nullable|array',
            'video_interview' => 'nullable|string|max:255',
            'other_communications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $activity = PreActivity::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Pre-activity created successfully',
                'data' => $activity,
                'id' => $activity->id
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pre-activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $activity = PreActivity::find($id);
        
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-activity not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    /**
     * Get activities by support type
     */
    public function getBySupportType($type)
    {
        $activities = PreActivity::whereJsonContains('support', $type)->get();
        
        return response()->json([
            'success' => true,
            'data' => $activities,
            'count' => $activities->count()
        ]);
    }
}
