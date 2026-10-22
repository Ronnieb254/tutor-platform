<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Booking Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#FBF5E6;font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;color:#15281F;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF5E6;padding:40px 16px;">
  <tr><td align="center">
    <table width="100%" style="max-width:520px;background:#fff;border:2px solid #15281F;border-radius:24px;overflow:hidden;">

      <tr><td style="background:#15281F;padding:28px 32px;text-align:center;">
        <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
          <tr>
            <td style="padding-right:10px;">
              <svg width="32" height="32" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="50" fill="#FBF5E6"/>
                <circle cx="50" cy="50" r="34" fill="#0A64BC"/>
                <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
              </svg>
            </td>
            <td><span style="font-size:22px;font-weight:700;color:#FBF5E6;">Mwalimu</span></td>
          </tr>
        </table>
      </td></tr>

      <tr><td style="padding:32px;">
        <h1 style="font-size:26px;font-weight:700;color:#15281F;margin:0 0 8px;">Booking Confirmed! ✅</h1>
        <p style="color:rgba(21,40,31,0.65);font-size:15px;margin:0 0 24px;">
          Hi {{ $booking->student->name }}, your session with <strong style="color:#15281F;">{{ $booking->tutor->name }}</strong> is confirmed and paid.
        </p>

        <table width="100%" style="background:#FBF5E6;border-radius:16px;padding:20px;border:1.5px solid rgba(21,40,31,0.1);margin-bottom:24px;">
          @foreach([
            ['Tutor', $booking->tutor->name],
            ['Subject', $booking->subject?->name ?? 'General Session'],
            ['Date', $booking->scheduled_at->format('l, F j, Y')],
            ['Time', $booking->scheduled_at->format('g:i a')],
            ['Duration', $booking->duration_minutes . ' minutes'],
            ['Amount Paid', 'KSh ' . number_format($booking->total_amount, 0)],
          ] as [$label, $value])
          <tr>
            <td style="padding:6px 0;color:rgba(21,40,31,0.55);font-size:13px;width:45%;">{{ $label }}</td>
            <td style="padding:6px 0;color:#15281F;font-size:13px;font-weight:600;">{{ $value }}</td>
          </tr>
          @endforeach
        </table>

        {{-- Session code --}}
        <div style="background:#F5F3FF;border:2px solid #0A64BC;border-radius:20px;padding:24px;text-align:center;margin-bottom:24px;">
          <p style="font-size:13px;font-weight:700;color:rgba(10,100,188,0.7);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 8px;">Your Session Access Code</p>
          <p style="font-family:monospace;font-size:32px;font-weight:800;color:#0A64BC;letter-spacing:0.12em;margin:0 0 8px;">{{ $booking->session_code_student }}</p>
          <p style="font-size:12px;color:rgba(21,40,31,0.5);margin:0;">Enter this code to join your live session</p>
        </div>

        <div style="background:#F0FDF4;border:1.5px solid #86EFAC;border-radius:16px;padding:16px;margin-bottom:24px;">
          <p style="font-size:13px;font-weight:700;color:#15803D;margin:0 0 6px;">📋 How to join your session</p>
          <ol style="font-size:13px;color:#166534;margin:0;padding-left:18px;line-height:1.8;">
            <li>Go to your Mwalimu dashboard</li>
            <li>Find this booking under "Upcoming Sessions"</li>
            <li>Click "Join Session" and enter your code: <strong>{{ $booking->session_code_student }}</strong></li>
            <li>Your live session opens in the browser — no downloads needed!</li>
          </ol>
        </div>

        <div style="text-align:center;margin-bottom:24px;">
          <a href="{{ route('bookings.show', $booking->uuid) }}"
             style="display:inline-block;background:#0A64BC;color:#fff;text-decoration:none;padding:14px 32px;border-radius:999px;font-weight:700;font-size:15px;border:1.5px solid #15281F;">
            View Booking Details
          </a>
        </div>

        <p style="font-size:12px;color:rgba(21,40,31,0.45);line-height:1.5;margin:0;border-top:1px solid rgba(21,40,31,0.1);padding-top:16px;">
          &copy; {{ date('Y') }} Mwalimu &mdash; Kenya's Tutor Marketplace
        </p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
