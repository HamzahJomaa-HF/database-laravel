<?php

// app/Livewire/ActivityUserForm.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ActivityUser;
use App\Models\Activity;
use App\Models\User;
use App\Models\Cop;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityUserForm extends Component
{
    // Form properties
    public $user_id = null;
    public $activity_id = null;
    public $cop_id = null;
    public $type = '';
    public $is_lead = false;
    public $invited = true;
    public $attended = false;
    public $embedMode = false;
    // UI state properties
    public $userSearch = '';
    public $activitySearch = '';
    public $showUserResults = false;
    public $showActivityResults = false;
    public $userResults = [];
    public $activityResults = [];
    
    // Selected items display
    public $selectedUserDisplay = null;
    public $selectedActivityDisplay = null;
    
    // For edit mode
    public $activityUser = null;
    public $isEditing = false;

    protected $rules = [
        'user_id' => 'required|exists:users,user_id',
        'activity_id' => 'required|exists:activities,activity_id',
        'cop_id' => 'nullable|exists:cops,cop_id',
        'type' => 'nullable|string|max:255',
        'is_lead' => 'boolean',
        'invited' => 'boolean',
        'attended' => 'boolean',
    ];

    public function mount($id = null)
{
    if ($id) {
        // Edit mode
        $this->activityUser = ActivityUser::with(['user', 'activity'])->findOrFail($id);
        $this->user_id = $this->activityUser->user_id;
        $this->activity_id = $this->activityUser->activity_id;
        $this->cop_id = $this->activityUser->cop_id;
        $this->type = $this->activityUser->type;
        $this->is_lead = $this->activityUser->is_lead;
        $this->invited = $this->activityUser->invited;
        $this->attended = $this->activityUser->attended;
        $this->isEditing = true;
        
        // Set selected display for user
        if ($this->activityUser->user) {
            $user = $this->activityUser->user;
            $fullName = trim($user->first_name . ' ' . 
                         ($user->middle_name ? $user->middle_name . ' ' : '') . 
                         $user->last_name);
            $this->selectedUserDisplay = [
                'id' => $user->user_id,
                'full_name' => $fullName,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'passport_number' => $user->passport_number,
                'identification_id' => $user->identification_id,
                'type' => ucfirst($user->type ?? 'Unknown'),
                'phone_number' => $user->phone_number,
                'dob' => $user->dob ? date('d M Y', strtotime($user->dob)) : null,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
            ];
        }
        
        // Set selected display for activity
        if ($this->activityUser->activity) {
            $activity = $this->activityUser->activity;
            $title = $activity->activity_title_en ?: $activity->activity_title_ar;
            $startDateStr = $activity->start_date ? date('M d, Y', strtotime($activity->start_date)) : '';
            $endDateStr = $activity->end_date ? date('M d, Y', strtotime($activity->end_date)) : '';
            
            $this->selectedActivityDisplay = [
                'id' => $activity->activity_id,
                'title' => $title,
                'title_en' => $activity->activity_title_en,
                'title_ar' => $activity->activity_title_ar,
                'type' => $activity->activity_type,
                'folder_name' => $activity->folder_name,
                'start_date' => $activity->start_date,
                'start_date_formatted' => $startDateStr,
                'end_date' => $activity->end_date,
                'end_date_formatted' => $endDateStr,
                'venue' => $activity->venue,
                'maximum_capacity' => $activity->maximum_capacity
            ];
        }
    }
}

    // Real-time user search
   public function updatedUserSearch()
{
    if (strlen($this->userSearch) < 1) {
        $this->showUserResults = false;
        $this->userResults = [];
        return;
    }

    $this->showUserResults = true;
    
    $this->userResults = User::query()
        ->where(function ($query) {
            $query->where('first_name', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('middle_name', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('last_name', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('email', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('phone_number', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('passport_number', 'ilike', "%{$this->userSearch}%")
                  ->orWhere('identification_id', 'ilike', "%{$this->userSearch}%"); // Added
        })
        ->orderBy('first_name')
        ->limit(10)
        ->get()
        ->map(function ($user) {
            $fullName = trim($user->first_name . ' ' . 
                         ($user->middle_name ? $user->middle_name . ' ' : '') . 
                         $user->last_name);
            
            // Determine badge class and icon based on user type
            $typeBadgeClass = 'bg-secondary';
            $typeIcon = 'fa-user';
            
            if ($user->type == 'beneficiary') {
                $typeBadgeClass = 'bg-success';
                $typeIcon = 'fa-hand-holding-heart';
            } elseif ($user->type == 'stakeholder') {
                $typeBadgeClass = 'bg-primary';
                $typeIcon = 'fa-building';
            } elseif ($user->type == 'trainer') {
                $typeBadgeClass = 'bg-warning text-dark';
                $typeIcon = 'fa-chalkboard-teacher';
            } elseif ($user->type == 'staff') {
                $typeBadgeClass = 'bg-info';
                $typeIcon = 'fa-user-tie';
            }
            
            return [
                'id' => $user->user_id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'full_name' => $fullName,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'dob' => $user->dob ? date('d M Y', strtotime($user->dob)) : null,
                'passport_number' => $user->passport_number,
                'identification_id' => $user->identification_id, // Added
                'type' => ucfirst($user->type ?? 'Unknown'),
                'type_badge_class' => $typeBadgeClass,
                'type_icon' => $typeIcon,
                'avatar' => $user->avatar
            ];
        })
        ->toArray();
}

   public function updatedActivitySearch()
{
    if (strlen($this->activitySearch) < 1) {
        $this->showActivityResults = false;
        $this->activityResults = [];
        return;
    }

    $this->showActivityResults = true;
    
    $this->activityResults = Activity::query()
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
            $endDateStr = $activity->end_date ? date('M d, Y', strtotime($activity->end_date)) : '';
            
            return [
                'id' => $activity->activity_id,
                'title' => $title,
                'title_en' => $activity->activity_title_en,
                'title_ar' => $activity->activity_title_ar,
                'type' => $activity->activity_type,
                'folder_name' => $activity->folder_name,
                'start_date' => $activity->start_date,
                'end_date' => $activity->end_date,
                'start_date_formatted' => $startDateStr,
                'end_date_formatted' => $endDateStr,
                'venue' => $activity->venue,
                'maximum_capacity' => $activity->maximum_capacity
            ];
        })
        ->toArray();
}

    public function selectUser($userId)
{
     
    $user = User::find($userId);
    if ($user) {
        $fullName = trim($user->first_name . ' ' . 
                     ($user->middle_name ? $user->middle_name . ' ' : '') . 
                     $user->last_name);
        
        $this->user_id = $user->user_id;
        $this->selectedUserDisplay = [
            'id' => $user->user_id,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'full_name' => $fullName,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'dob' => $user->dob ? date('d M Y', strtotime($user->dob)) : null,
            'passport_number' => $user->passport_number,
            'identification_id' => $user->identification_id, // Added
            'type' => ucfirst($user->type ?? 'Unknown'),
            'avatar' => $user->avatar
        ];
        $this->userSearch = '';
        $this->showUserResults = false;
    }
}

    // Clear user selection
    public function clearUserSelection()
    {
        $this->user_id = null;
        $this->selectedUserDisplay = null;
        $this->userSearch = '';
        $this->showUserResults = false;
    }

    // Select activity
   public function selectActivity($activityId)
{
     
    
    $activity = Activity::find($activityId);
    if ($activity) {
        $title = $activity->activity_title_en ?: $activity->activity_title_ar;
        $startDateStr = $activity->start_date ? date('M d, Y', strtotime($activity->start_date)) : '';
        $endDateStr = $activity->end_date ? date('M d, Y', strtotime($activity->end_date)) : '';
        
        $this->activity_id = $activity->activity_id;
        $this->selectedActivityDisplay = [
            'id' => $activity->activity_id,
            'title' => $title,
            'title_en' => $activity->activity_title_en,
            'title_ar' => $activity->activity_title_ar,
            'type' => $activity->activity_type,
            'folder_name' => $activity->folder_name,
            'start_date' => $activity->start_date,
            'end_date' => $activity->end_date,
            'start_date_formatted' => $startDateStr,
            'end_date_formatted' => $endDateStr,
            'venue' => $activity->venue,
            'maximum_capacity' => $activity->maximum_capacity
        ];
        $this->activitySearch = '';
        $this->showActivityResults = false;
        if ($this->embedMode) {
                $this->dispatch('activity-selected', activityId: $this->activity_id);
       } 
    }
}

    // Clear activity selection
    public function clearActivitySelection()
    {
        $this->activity_id = null;
        $this->selectedActivityDisplay = null;
        $this->activitySearch = '';
        $this->showActivityResults = false;
        if ($this->embedMode) {
            $this->dispatch('activity-cleared');
        }
    }

    // Hide results (called from JavaScript)
    public function hideResults()
    {
        $this->showUserResults = false;
        $this->showActivityResults = false;
    }

    // Reset form
    public function resetForm()
    {
        $this->clearUserSelection();
        $this->clearActivitySelection();
        $this->cop_id = null;
        $this->type = '';
        $this->is_lead = false;
        $this->invited = true;
        $this->attended = false;
    }

    public function save()
{
    

    
    $this->validate();

    try {
        DB::beginTransaction();

        if ($this->isEditing) {
           
            
            // Update existing - INCLUDE ALL FIELDS
            $updateData = [
                'user_id' => $this->user_id,
                'activity_id' => $this->activity_id,
                'cop_id' => $this->cop_id,
                'type' => $this->type,
                'is_lead' => $this->is_lead,
                'invited' => $this->invited,
                'attended' => $this->attended,
            ];
            
           
            $updated = $this->activityUser->update($updateData);
            
            
            // Refresh the model to get latest data from database
            $this->activityUser->refresh();
            
          
            
            $message = 'Activity-User relationship updated successfully!';
        } else {
            
            // Check if relationship already exists
            $exists = ActivityUser::where('user_id', $this->user_id)
                ->where('activity_id', $this->activity_id)
                ->exists();

            if ($exists) {
                Log::info('Relationship already exists, aborting');
                session()->flash('error', 'This user is already assigned to this activity.');
                return;
            }

            // Create new
            $createData = [
                'activity_user_id' => (string) Str::uuid(),
                'user_id' => $this->user_id,
                'activity_id' => $this->activity_id,
                'cop_id' => $this->cop_id,
                'type' => $this->type,
                'is_lead' => $this->is_lead,
                'invited' => $this->invited,
                'attended' => $this->attended,
            ];
            
            
            $activityUser = ActivityUser::create($createData);
            
            
            $message = 'Activity-User relationship created successfully!';
        }

        DB::commit();

        session()->flash('success', $message);
        
       
        return redirect()->route('activity-users.index');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('EXCEPTION in save method: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        session()->flash('error', 'Failed to save relationship: ' . $e->getMessage());
    }
}
    public function render()
    {
        $cops = Cop::orderBy('cop_name')->get();
        
        return view('livewire.activity-user-form', [
            'cops' => $cops
        ]);
    }
}