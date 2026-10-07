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
        'logistics' => [
            'label' => 'Logistics',
            'action' => 'Kindly proceed with the necessary logistics arrangements related to this activity, including venue setup, equipment, transportation, and coordination as applicable.',
        ],
        'data' => [
            'label' => 'Data',
            'action' => 'Kindly proceed with the necessary data support related to this activity, including registration, data collection, reporting, and coordination as applicable.',
        ],
        'public_relations' => [
            'label' => 'Public Relations',
            'action' => 'Kindly proceed with the necessary public relations preparations related to this activity, including stakeholder communication, invitations, and coordination as applicable.',
        ],
        'media' => [
            'label' => 'Media and Communications',
            'action' => 'Kindly proceed with the necessary media and communications preparations related to this activity, including coverage, content development, and coordination as applicable.',
        ],
        'field_support' => [
            'label' => 'Facilitation & Field Support',
            'action' => 'Kindly proceed with the necessary facilitation and field support preparations related to this activity, including on-site assistance and coordination as applicable.',
        ],
    ];

    public function __construct(
        public Activity $activity,
        public string $submittedBy,
        public string $supportKey,
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
            ->view('emails.activity-support-requested')
            ->with([
                'activity' => $this->activity,
                'submittedBy' => $this->submittedBy,
                'supportLabel' => $support['label'],
                'supportAction' => $support['action'],
            ]);
    }
}
