<?php

namespace App\Mail;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivityFocalPointAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Activity $activity,
        public string $submittedBy,
    ) {
    }

    public function build()
    {
        $title = trim($this->activity->activity_title_en . ' - ' . $this->activity->activity_title_ar, ' -');

        return $this
            ->subject('New Activity Submitted: ' . $title)
            ->view('emails.activity-focal-point-assigned')
            ->with([
                'activity' => $this->activity,
                'submittedBy' => $this->submittedBy,
                'activityUrl' => route('activities.edit', $this->activity->activity_id),
            ]);
    }
}
