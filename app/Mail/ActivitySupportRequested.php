<?php

namespace App\Mail;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivitySupportRequested extends Mailable
{
    use Queueable, SerializesModels;

    public const SUPPORT = [
        'dmesc' => [
            'label' => 'Data, Monitoring & Evaluation & Stakeholder-Communications',
            'action' => 'Kindly proceed with the requested items listed below for this activity.',
        ],
        'logproc' => [
            'label' => 'Logistics & Procurement',
            'action' => 'Kindly proceed with the requested items listed below for this activity.',
        ],
        'comms' => [
            'label' => 'Communications',
            'action' => 'Kindly review the requested items listed below and assess the services and needs accordingly, based on the requirements of the event.',
        ],
        'president' => [
            'label' => 'President Involvement',
            'action' => 'Kindly note that the President is involved in the activity below. Please take the necessary preparations and coordination into consideration.',
        ],
        'venue' => [
            'label' => 'Venue',
            'action' => 'Kindly note that the venue listed below has been requested for this activity. Please proceed with the necessary arrangements and confirm availability.',
        ],
        'technical' => [
            'label' => 'Technical Support (Live Streaming, Hardware, WiFi)',
            'action' => 'Kindly proceed with the requested technical items listed below for this activity.',
        ],
    ];

    public function __construct(
        public Activity $activity,
        public string $submittedBy,
        public string $supportKey,
        public array $details = [],
        public ?string $recipientName = null,
    ) {
    }

    public function build()
    {
        $support = self::SUPPORT[$this->supportKey] ?? [
            'label' => ucfirst(str_replace('_', ' ', $this->supportKey)),
            'action' => 'Kindly proceed with the necessary preparations related to this activity.',
        ];
        $title = trim($this->activity->activity_title_en . ' - ' . $this->activity->activity_title_ar, ' -');

        return $this
            ->subject($support['label'] . ' Support Required: ' . $title)
            ->view(match ($this->supportKey) {
                'venue' => 'emails.activity-venue-requested',
                'president' => 'emails.activity-president-involved',
                default => 'emails.activity-support-requested',
            })
            ->with([
                'activity' => $this->activity,
                'submittedBy' => $this->submittedBy,
                'supportLabel' => $support['label'],
                'supportAction' => $support['action'],
                'details' => $this->details,
                'recipientName' => $this->recipientName,
            ]);
    }
}
