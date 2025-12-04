<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;   // << add this

class ActivityController extends Controller
{
    public function index($external_activity_id)
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
