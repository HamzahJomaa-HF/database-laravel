<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#1e1e1e; font-family: Segoe UI, Arial, sans-serif; color:#f2f2f2;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#1e1e1e; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:640px; background-color:#1e1e1e; color:#f2f2f2; font-size:15px; line-height:1.6;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:0 16px;">
                            <p style="margin:0 0 16px;">Dear Team,</p>

                            <p style="margin:0 0 16px;">A new activity has been submitted.</p>

                            <p style="margin:0;">
                                <strong>Title:</strong> {{ $activity->activity_title_en }} - {{ $activity->activity_title_ar }}
                            </p>
                            <p style="margin:0 0 16px;">
                                <strong>Submitted by:</strong> {{ $submittedBy }}
                            </p>

                            <p style="margin:0 0 12px;"><strong>Submitted Details:</strong></p>

                            <table role="presentation" width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; border:1px solid #555; margin-bottom:16px;">
                                <tr>
                                    <td style="border:1px solid #555; font-weight:bold; width:35%;">Field</td>
                                    <td style="border:1px solid #555; font-weight:bold;">Value</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">Activity Type</td>
                                    <td style="border:1px solid #555;">{{ $activity->activity_type }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">Content Network</td>
                                    <td style="border:1px solid #555;">{{ $activity->content_network }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">Start Date</td>
                                    <td style="border:1px solid #555;">{{ optional($activity->start_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">End Date</td>
                                    <td style="border:1px solid #555;">{{ optional($activity->end_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">Venue</td>
                                    <td style="border:1px solid #555;">{{ $activity->venue }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #555;">Maximum Capacity</td>
                                    <td style="border:1px solid #555;">{{ $activity->maximum_capacity }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px;">
                                Kindly visit the link below to assign it to the action plan:
                                <a href="{{ $activityUrl }}" style="color:#6cb6ff;">{{ $activityUrl }}</a>
                            </p>

                            <p style="margin:0;">Thank you.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
