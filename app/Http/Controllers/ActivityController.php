<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;   // << add this

class ActivityController extends Controller
{
        /**
     * Display a listing of activities.
     */
    public function index(Request $request)
    {
        // Start query
        $query = Activity::query();

        // Check if any search parameters exist
        $hasSearch = $request->anyFilled([
            'title',
            'activity_type',
            'venue',
            'status',
            'start_date_from',
            'end_date_to'
        ]);

        // Apply filters
        if ($request->filled('title')) {
            $query->where(function ($q) use ($request) {
                $q->where('activity_title_en', 'like', '%' . $request->title . '%')
                    ->orWhere('activity_title_ar', 'like', '%' . $request->title . '%');
            });
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('venue')) {
            $query->where('venue', 'like', '%' . $request->venue . '%');
        }

        if ($request->filled('status')) {
            // Apply status logic based on dates
            $now = now();
            if ($request->status == 'upcoming') {
                $query->where('start_date', '>', $now);
            } elseif ($request->status == 'ongoing') {
                $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            } elseif ($request->status == 'completed') {
                $query->where('end_date', '<', $now);
            } elseif ($request->status == 'cancelled') {
                // Assuming you have an is_cancelled column
                $query->where('is_cancelled', true);
            }
        }

        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }

        if ($request->filled('end_date_to')) {
            $query->where('end_date', '<=', $request->end_date_to);
        }

        // Get paginated results
        $activities = $query->orderBy('start_date', 'desc')->paginate(20);

        return view('activities.index', compact('activities', 'hasSearch'));
    }


    


    public function edit($external_activity_id)
    {
        // Fetch the activity by external_activity_id
        $activity = Activity::where('external_id', $external_activity_id)->first();

        // If not found → a simple 404 page or redirect
        if (!$activity) {
            abort(404, 'Activity not found');
        }

        // Return the Blade view with the activity data
        return view('Activities.link', compact('activity'));
    }
}
