<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Mwalimu Profile is Live!</title>
</head>
<body style="margin:0;padding:0;background:#F4F7F4;font-family:'DM Sans',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F4;padding:40px 20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#15281F;border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
                        <div style="font-family:Georgia,serif;font-size:28px;font-weight:700;color:#fff;margin-bottom:4px;">Mwalimu</div>
                        <div style="font-size:13px;color:rgba(255,255,255,.5);">Kenya's Premier Tutoring Platform</div>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="background:#fff;padding:40px;">
                        <div style="font-size:32px;text-align:center;margin-bottom:16px;">Ã°Å¸Å½â€°</div>
                        <h1 style="font-family:Georgia,serif;font-size:24px;font-weight:700;color:#15281F;margin:0 0 16px;text-align:center;">
                            Your Profile is Live!
                        </h1>
                        <p style="font-size:15px;color:#15281F;line-height:1.7;margin:0 0 16px;">
                            Hello {{ $user->name }},
                        </p>
                        <p style="font-size:15px;color:#374151;line-height:1.7;margin:0 0 20px;">
                            Congratulations! Your tutor profile has been <strong>verified and approved</strong> by our team. Your profile is now live on Mwalimu and students can start booking sessions with you.
                        </p>

                        {{-- What's next box --}}
                        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:12px;padding:20px 24px;margin:0 0 24px;">
                            <div style="font-weight:700;color:#166534;font-size:14px;margin-bottom:12px;">
                                Ã¢Å“â€¦ What's next
                            </div>
                            <ul style="margin:0;padding-left:18px;color:#374151;font-size:14px;line-height:1.9;">
                                <li>Set your weekly availability in your dashboard</li>
                                <li>Complete your bio so students know what to expect</li>
                                <li>Students can now find and book you directly</li>
                                <li>You'll receive an email and SMS when a booking is made</li>
                            </ul>
                        </div>

                        <div style="text-align:center;margin:28px 0;">
                            <a href="{{ url('/dashboard/tutor') }}"
                               style="display:inline-block;padding:14px 36px;background:#0A64BC;color:#fff;font-size:15px;font-weight:700;text-decoration:none;border-radius:999px;">
                                Go to My Dashboard
                            </a>
                        </div>

                        <p style="font-size:13px;color:#6B7280;line-height:1.7;margin:0;">
                            If you have any questions, reply to this email or contact us at
                            <a href="mailto:support@ovaldesk.co.ke" style="color:#0A64BC;">support@ovaldesk.co.ke</a>.
                        </p>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#F9FAF9;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;border-top:1px solid #E5E7EB;">
                        <p style="font-size:12px;color:#9CA3AF;margin:0;">
                            Ã‚Â© {{ date('Y') }} Mwalimu Ã‚Â· Oval Desk Ventures Ã‚Â· Nairobi, Kenya
                        </p>
                    </td>
                </tr>

            </table>
        </td></tr>
    </table>
</body>
</html>
