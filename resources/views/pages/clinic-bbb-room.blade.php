<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $clinic->title ?? $clinic->name }} - Clinic Session</title>
<style>
:root{--ink:#15281F;--trust-blue:#0A64BC;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*{margin:0;padding:0;box-sizing:border-box;}
body,html{width:100%;height:100%;font-family:'DM Sans',sans-serif;background:var(--ink);}

.bbb-container{width:100%;height:100vh;display:flex;flex-direction:column;}
.bbb-room{flex:1;width:100%;background:#000;border:none;}

.session-header{background:var(--ink);padding:12px 20px;border-bottom:1px solid var(--hair);display:flex;justify-content:space-between;align-items:center;color:#fff;font-size:13px;z-index:100;}
.session-title{font-weight:700;flex:1;}
.session-code{background:rgba(10,100,188,.2);padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.moderator-badge{background:rgba(234,179,8,.2);border:1px solid rgba(234,179,8,.4);color:#facc15;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;margin-left:16px;}
.exit-btn{background:var(--hair);color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:all .15s;}
.exit-btn:hover{background:rgba(255,255,255,.15);}

.access-warning{position:fixed;bottom:20px;right:20px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:var(--mint);padding:12px 16px;border-radius:8px;font-size:12px;display:flex;align-items:center;gap:8px;z-index:60;}
</style>
</head>
<body>

<div class="bbb-container">
    <div class="session-header">
        <div style="display:flex;align-items:center;flex:1;">
            <div class="session-title">
                <i class="fa-solid fa-chalkboard-user"></i> {{ $clinic->title ?? $clinic->name }}
            </div>
            <span style="color:var(--muted);margin:0 12px;font-size:10px;">•</span>
            <span style="color:var(--muted);">{{ $clinic->scheduled_at->format('g:ia') }}</span>
            @if($isModerator)
                <span class="moderator-badge">
                    <i class="fa-solid fa-shield-check"></i> MODERATOR
                </span>
            @else
                <span class="session-code">{{ $sessionCode }}</span>
            @endif
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="exit-btn">
                <i class="fa-solid fa-sign-out-alt" style="margin-right:6px;"></i> Leave Session
            </button>
        </form>
    </div>

    @if(!empty($joinUrl))
    <iframe class="bbb-room"
            src="{{ $joinUrl }}"
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            allowfullscreen>
    </iframe>
    @else
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;text-align:center;">
        <i class="fa-solid fa-video" style="font-size:48px;color:rgba(255,255,255,.15);margin-bottom:18px;"></i>
        <h3 style="font-family:'Fraunces',serif;font-size:22px;color:#fff;margin-bottom:8px;">Session Link Not Available</h3>
        <p style="font-size:14px;color:rgba(255,255,255,.5);max-width:380px;">The session room link is not ready yet. Please refresh the page or contact support if this persists.</p>
    </div>
    @endif
</div>

<div class="access-warning">
    <i class="fa-solid fa-shield-check"></i>
    <span>Platform-only session · Powered by BigBlueButton · Encrypted</span>
</div>

</body>
</html>
