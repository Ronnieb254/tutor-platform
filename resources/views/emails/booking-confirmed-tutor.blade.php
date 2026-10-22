<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>New Session Booked</title>
</head>
<body style="margin:0;padding:0;background:#FBF5E6;font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;color:#15281F;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF5E6;padding:40px 16px;">
  <tr><td align="center">
    <table width="100%" style="max-width:520px;background:#fff;border:2px solid #15281F;border-radius:24px;overflow:hidden;">

      {{-- Header --}}
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

      {{-- Body --}}
      <tr><td style="padding:32px;">
        <h1 style="font-size:26px;font-weight:700;color:#15281F;margin:0 0 8px;">New Session Booked! 🎉</h1>
        <p style="color:rgba(21,40,31,0.65);font-size:15px;margin:0 0 24px;">
          Great news, {{ $booking->tutor->name }}! You have a new booking from <strong style="color:#15281F;">{{ $booking->student->name }}</strong>.
        </p>

        {{-- Booking details --}}
        <table width="100%" style="background:#FBF5E6;border-radius:16px;padding:20px;border:1.5px solid rgba(21,40,31,0.1);margin-bottom:24px;">
          @foreach([
            ['Student', $booking->student->name],
            ['Subject', $booking->subject?->name ?? 'General Session'],
            ['Date', $booking->scheduled_at->format('l, F j, Y')],
            ['Time', $booking->scheduled_at->format('g:i a')],
            ['Duration', $booking->duration_minutes . ' minutes'],
            ['Your Earnings', 'KSh ' . number_format($booking->tutor_earning, 0)],
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
          <p style="font-family:monospace;font-size:32px;font-weight:800;color:#0A64BC;letter-spacing:0.12em;margin:0 0 8px;">{{ $booking->session_code_tutor }}</p>
          <p style="font-size:12px;color:rgba(21,40,31,0.5);margin:0;">Use this code to enter the live session room</p>
        </div>

        <p style="font-size:14px;color:rgba(21,40,31,0.7);margin:0 0 20px;line-height:1.6;">
          On the day of the session, visit your dashboard and click "Join Session", or go to the booking page and enter the code above.
        </p>

        <div style="text-align:center;margin-bottom:24px;">
          <a href="{{ route('tutor.dashboard') }}"
             style="display:inline-block;background:#0A64BC;color:#fff;text-decoration:none;padding:14px 32px;border-radius:999px;font-weight:700;font-size:15px;border:1.5px solid #15281F;">
            View Booking
          </a>
        </div>

        <p style="font-size:12px;color:rgba(21,40,31,0.45);line-height:1.5;margin:0;border-top:1px solid rgba(21,40,31,0.1);padding-top:16px;">
          You're receiving this because a student booked a session with you on Mwalimu.
          &copy; {{ date('Y') }} Mwalimu &mdash; Kenya's Tutor Marketplace
        </p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
