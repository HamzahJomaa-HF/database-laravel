<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreActivity extends Model
{
    use HasFactory;

    protected $table = 'pre_activities';

    protected $fillable = [
        // Basic Information
        'name_en',
        'name_ar',
        'support',
        'focal_point_username',
        
        // Main Data
        'project',
        'partners',
        'location',
        'venue_availability',
        'activity_date',
        'activity_time',
        'activity_date_time',
        'sessions_number',
        'activity_type',
        'participants_number',
        
        // Data, Monitoring & Evaluation
        'invitations',
        'attendance_list',
        'mom_list',
        'registration_form',
        'post_evaluation',
        'certificate_type',
        
        // Logistics & Procurement
        'sound_system',
        'zoom_type',
        'setup',
        'setup_type',
        'screen_type',
        'hardware',
        'wifi',
        'rollups_needed',
        'flags_needed',
        'podium',
        'food',
        'note_papers',
        'staff_names',
        'volunteer_numbers',
        'support_areas',
        
        // Communications
        'coverage',
        'media',
        'video_interview',
        'other_communications',
        'notes',
    ];

    protected $casts = [
        'support' => 'array',
        'invitations' => 'array',
        'attendance_list' => 'array',
        'mom_list' => 'array',
        'certificate_type' => 'array',
        'sound_system' => 'array',
        'setup' => 'array',
        'screen_type' => 'array',
        'hardware' => 'array',
        'wifi' => 'array',
        'food' => 'array',
        'coverage' => 'array',
        'media' => 'array',
        'activity_date' => 'date',
        'activity_date_time' => 'datetime',
    ];

    // Helper method to check support type
    public function hasSupportType($type)
    {
        return in_array($type, $this->support ?? []);
    }

    // Scope for different support types
    public function scopeWithSupportType($query, $type)
    {
        return $query->whereJsonContains('support', $type);
    }
}