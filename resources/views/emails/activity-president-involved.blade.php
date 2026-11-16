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
                            <p style="margin:0 0 16px;">Dear {{ $recipientName ?: 'Team' }},</p>

                            <p style="margin:0 0 16px;">We would like to kindly inform you that Mrs. Bahia Hariri is expected to attend the following event:</p>

                            <p style="margin:0 0 12px;"><strong>Event Details:</strong></p>
                            <p style="margin:0;"><strong>Event Name:</strong> {{ trim($activity->activity_title_en . ' - ' . $activity->activity_title_ar, ' -') }}</p>
                            <p style="margin:0;"><strong>Date:</strong> {{ optional($activity->start_date)->format('Y-m-d') }}@if($activity->end_date && $activity->end_date != $activity->start_date) to {{ $activity->end_date->format('Y-m-d') }}@endif</p>
                            <p style="margin:0 0 16px;"><strong>Venue:</strong> {{ $activity->venue }}</p>

                            <p style="margin:0;">Thank you.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
