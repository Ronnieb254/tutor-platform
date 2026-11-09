<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject }}</title>
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
 <h1 style="font-size:26px;font-weight:700;color:#15281F;margin:0 0 8px;">{{ $subject }}</h1>
 <p style="color:rgba(21,40,31,0.65);font-size:15px;margin:0 0 24px;line-height:1.6;">
 {!! nl2br(e($body)) !!}
 </p>

 <p style="font-size:12px;color:rgba(21,40,31,0.45);line-height:1.5;margin:0;border-top:1px solid rgba(21,40,31,0.1);padding-top:16px;">
 You are receiving this message from the Mwalimu admin team.
 &copy; {{ date('Y') }} Mwalimu &mdash; Kenya's Tutor Marketplace
 </p>
 </td></tr>
 </table>
 </td></tr>
</table>
</body>
</html>
