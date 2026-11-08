<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
 <link rel="icon" type="image/svg+xml" href="/favicon.svg">
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
 $earning = match($delivery) {
 'not_delivered' => 0,
 'partially_delivered' => round((float) $booking->tutor_earning * 0.5, 2),
 default => (float) $booking->tutor_earning,
 };
 @endphp

 <h1 style="font-size:24px;font-weight:700;color:#15281F;margin:0 0 8px;">
 @if($delivery === 'fully_delivered') Å-… Session Completed â‚¬" Earnings Credited
 @elseif($delivery === 'partially_delivered') ⚠ Ã¯Â¸Â Session Partially Delivered
 @else ÂÅ’ Session Not Delivered @endif
 </h1>

 <p style="color:rgba(21,40,31,0.65);font-size:15px;margin:0 0 24px;">
 Hi {{ $booking->tutor->name }},
 @if($delivery === 'fully_delivered')
 your session with <strong>{{ $booking->student->name }}</strong> has been marked complete.
 @elseif($delivery === 'partially_delivered')
 your session with <strong>{{ $booking->student->name }}</strong> was partially delivered (less than 80% of scheduled time).
 @else
 the system did not detect your attendance for the session with <strong>{{ $booking->student->name }}</strong>.
 @endif
 </p>

 <table width="100%" style="background:#FBF5E6;border-radius:16px;padding:20px;border:1.5px solid rgba(21,40,31,0.1);margin-bottom:24px;">
 @php $rows = [
 ['Student', $booking->student->name],
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
 <tr>
 <td style="padding:6px 0;color:rgba(21,40,31,0.55);font-size:13px;">Delivery Status</td>
 <td style="padding:6px 0;font-size:13px;font-weight:700;
 @if($delivery === 'fully_delivered') color:#10B981;
 @elseif($delivery === 'partially_delivered') color:#EEBE15;
 @else color:#E8522A; @endif">
 {{ ucwords(str_replace('_', ' ', $delivery)) }}
 </td>
 </tr>
 <tr>
 <td style="padding:6px 0;color:rgba(21,40,31,0.55);font-size:13px;">Earnings Credited</td>
 <td style="padding:6px 0;color:{{ $earning > 0 ? '#10B981' : '#E8522A' }};font-size:13px;font-weight:700;">
 KSh {{ number_format($earning, 0) }}
 </td>
 </tr>
 </table>

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
