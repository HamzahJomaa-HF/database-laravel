<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;

class ActivitySelector extends Component
{
    public $activitySearch = '';
    public $showResults = false;
    public $results = [];
    public $selectedActivity = null;
    public $selectedActivityId = null;

    public function updatedActivitySearch()
    {
        if (strlen($this->activitySearch) < 1) {
            $this->showResults = false;
            $this->results = [];
            return;
        }

        $this->showResults = true;
        
        try {
            $this->results = Activity::query()
                ->where(function ($query) {
                    $query->where('activity_title_en', 'ilike', "%{$this->activitySearch}%")
                          ->orWhere('activity_title_ar', 'ilike', "%{$this->activitySearch}%")
                          ->orWhere('activity_type', 'ilike', "%{$this->activitySearch}%")
                          ->orWhere('venue', 'ilike', "%{$this->activitySearch}%")
                          ->orWhere('folder_name', 'ilike', "%{$this->activitySearch}%");
                })
                ->orderBy('activity_title_en')
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    $title = $activity->activity_title_en ?: $activity->activity_title_ar;
                    $startDateStr = $activity->start_date ? date('M d, Y', strtotime($activity->start_date)) : '';
                    
                    return [
                        'id' => $activity->activity_id,
                        'title' => $title,
                        'title_en' => $activity->activity_title_en,
                        'title_ar' => $activity->activity_title_ar,
                        'type' => $activity->activity_type,
                        'folder_name' => $activity->folder_name,
                        'start_date' => $activity->start_date,
                        'start_date_formatted' => $startDateStr,
                        'end_date' => $activity->end_date,
                        'end_date_formatted' => $activity->end_date ? date('M d, Y', strtotime($activity->end_date)) : '',
                        'venue' => $activity->venue,
                        'max_capacity' => $activity->maximum_capacity
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Activity search error: ' . $e->getMessage());
            $this->results = [];
        }
    }

    public function selectActivity($activityId)
    {
        try {
            $activity = Activity::find($activityId);
            if ($activity) {
                $title = $activity->activity_title_en ?: $activity->activity_title_ar;
                $startDateStr = $activity->start_date ? date('M d, Y', strtotime($activity->start_date)) : '';
                
                $this->selectedActivity = [
                    'id' => $activity->activity_id,
                    'title' => $title,
                    'title_en' => $activity->activity_title_en,
                    'title_ar' => $activity->activity_title_ar,
                    'type' => $activity->activity_type,
                    'folder_name' => $activity->folder_name,
                    'start_date' => $activity->start_date,
                    'start_date_formatted' => $startDateStr,
                    'end_date' => $activity->end_date,
                    'end_date_formatted' => $activity->end_date ? date('M d, Y', strtotime($activity->end_date)) : '',
                    'venue' => $activity->venue,
                    'max_capacity' => $activity->maximum_capacity
                ];
                $this->selectedActivityId = $activity->activity_id;
                $this->activitySearch = '';
                $this->showResults = false;
                
                // Dispatch browser event for the parent form
                $this->dispatchBrowserEvent('activity-selected', ['activityId' => $this->selectedActivityId]);
            }
        } catch (\Exception $e) {
            Log::error('Activity selection error: ' . $e->getMessage());
            // Optionally show error to user
            session()->flash('error', 'Error selecting activity');
        }
    }

    public function clearSelection()
    {
        try {
            $this->selectedActivity = null;
            $this->selectedActivityId = null;
            $this->activitySearch = '';
            $this->showResults = false;
            
            // Dispatch browser event for the parent form
            $this->dispatchBrowserEvent('activity-cleared');
            
            // Also emit a Livewire event
            $this->dispatch('activity-cleared');
            
        } catch (\Exception $e) {
            Log::error('Clear selection error: ' . $e->getMessage());
        }
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.activity-selector');
    }
}