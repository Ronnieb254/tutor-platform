<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Session Completed</title>
</head>
<body style="margin:0;padding:0;background:#FBF5E6;font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;color:#15281F;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#FBF5E6;padding:40px 16px;">
  <tr><td align="center">
    <table width="100%" style="max-width:520px;background:#fff;border:2px solid #15281F;border-radius:24px;overflow:hidden;">

      <tr><td style="background:#15281F;padding:28px 32px;text-align:center;">
        <span style="font-size:22px;font-weight:700;color:#FBF5E6;">Mwalimu</span>
      </td></tr>

      <tr><td style="padding:32px;">
        @php
          $delivery = $booking->delivery_status ?? 'fully_delivered';
          $iconColor = $delivery === 'fully_delivered' ? '#10B981' : ($delivery === 'not_delivered' ? '#E8522A' : '#EEBE15');
          $icon = $delivery === 'fully_delivered' ? '✅' : ($delivery === 'not_delivered' ? '❌' : '⚠️');
        @endphp

        <h1 style="font-size:24px;font-weight:700;color:#15281F;margin:0 0 8px;">{{ $icon }} Session
          @if($delivery === 'fully_delivered') Completed!
          @elseif($delivery === 'partially_delivered') Partially Delivered
          @else Not Delivered @endif
        </h1>

        <p style="color:rgba(21,40,31,0.65);font-size:15px;margin:0 0 24px;">
          Hi {{ $booking->student->name }},
          @if($delivery === 'fully_delivered')
            your session with <strong>{{ $booking->tutor->name }}</strong> has been completed successfully.
          @elseif($delivery === 'partially_delivered')
            your session with <strong>{{ $booking->tutor->name }}</strong> was partially delivered. A 50% refund has been credited to your wallet.
          @else
            unfortunately your tutor did not join the session. A full refund has been credited to your Mwalimu wallet.
          @endif
        </p>

        <table width="100%" style="background:#FBF5E6;border-radius:16px;padding:20px;border:1.5px solid rgba(21,40,31,0.1);margin-bottom:24px;">
          @php $rows = [
            ['Tutor', $booking->tutor->name],
            ['Subject', $booking->subject?->name ?? 'General Session'],
            ['Date', $booking->scheduled_at->format('l, F j, Y')],
            ['Duration', $booking->duration_minutes . ' min (scheduled)'],
          ]; @endphp
          @foreach($rows as [$label, $value])
          <tr>
            <td style="padding:6px 0;color:rgba(21,40,31,0.55);font-size:13px;width:45%;">{{ $label }}</td>
            <td style="padding:6px 0;color:#15281F;font-size:13px;font-weight:600;">{{ $value }}</td>
          </tr>
          @endforeach
          @if($delivery !== 'fully_delivered')
          <tr>
            <td style="padding:6px 0;color:rgba(21,40,31,0.55);font-size:13px;">Wallet Refund</td>
            <td style="padding:6px 0;color:#10B981;font-size:13px;font-weight:700;">
              KSh {{ $delivery === 'not_delivered' ? number_format($booking->total_amount, 0) : number_format($booking->total_amount * 0.5, 0) }} credited
            </td>
          </tr>
          @endif
        </table>

        @if($delivery === 'fully_delivered')
        <div style="background:#F0FDF4;border:1.5px solid #86EFAC;border-radius:16px;padding:16px;margin-bottom:24px;">
          <p style="font-size:13px;font-weight:700;color:#15803D;margin:0 0 6px;">Leave a Review</p>
          <p style="font-size:13px;color:#166534;margin:0;">Help other students by rating your session with {{ $booking->tutor->name }}.</p>
        </div>
        @endif

        <div style="text-align:center;margin-bottom:24px;">
          <a href="{{ route('bookings.show', $booking->uuid) }}"
             style="display:inline-block;background:#0A64BC;color:#fff;text-decoration:none;padding:14px 32px;border-radius:999px;font-weight:700;font-size:15px;">
            View Session Details
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
