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

                            <p style="margin:0 0 16px;">{{ $supportAction }}</p>

                            <p style="margin:0 0 12px;"><strong>Activity Details</strong></p>
                            <p style="margin:0;"><strong>Activity Title (EN):</strong> {{ $activity->activity_title_en }}</p>
                            <p style="margin:0;"><strong>Activity Title (AR):</strong> {{ $activity->activity_title_ar }}</p>
                            <p style="margin:0;"><strong>Activity Type:</strong> {{ $activity->activity_type }}</p>
                            <p style="margin:0;"><strong>Maximum Capacity:</strong> {{ $activity->maximum_capacity }}</p>
                            <p style="margin:0;"><strong>Start Date:</strong> {{ optional($activity->start_date)->format('Y-m-d') }}</p>
                            <p style="margin:0;"><strong>End Date:</strong> {{ optional($activity->end_date)->format('Y-m-d') }}</p>
                            <p style="margin:0;"><strong>Venue:</strong> {{ $activity->venue }}</p>
                            <p style="margin:0;"><strong>Content / Network:</strong> {{ $activity->content_network }}</p>
                            <p style="margin:0 0 16px;"><strong>Experts:</strong> {{ $activity->experts }}</p>

                            <p style="margin:0;">Thank you.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
