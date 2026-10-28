<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Create Group Session – Mwalimu Tutor Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--trust-blue:#0A64BC;--coral:#E8522A;--gold:#EEBE15;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}
.layout{display:flex;height:100vh;overflow:hidden;}
.sidebar{width:256px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;position:relative;z-index:50;}
.sb-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,.3);margin-top:1px;}
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-section{margin-bottom:18px;}
.sb-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.28);padding:0 10px;margin-bottom:5px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--trust-blue);color:#fff;}
.sb-badge{margin-left:auto;background:var(--coral);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;min-width:18px;text-align:center;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-uname{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;}
.sb-logout:hover{background:rgba(255,255,255,.16);color:#fff;}
.mob-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;}
@media(max-width:1023px){.sidebar{position:fixed;left:-256px;top:0;bottom:0;transition:left .25s;z-index:50;}.sidebar.open{left:0;}.mob-overlay.show{display:block;}}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.mob-toggle{display:none;width:34px;height:34px;border-radius:9px;border:1px solid var(--hair);background:#fff;align-items:center;justify-content:center;color:var(--muted);cursor:pointer;}
@media(max-width:1023px){.mob-toggle{display:flex;}}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--ink);}
.content{flex:1;overflow-y:auto;padding:24px;}
.field{margin-bottom:20px;}
.field label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
.field label span.req{color:var(--coral);}
.field input,.field select,.field textarea{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--hair);font-size:14px;font-family:inherit;color:var(--ink);outline:none;background:#fff;transition:border-color .15s;}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--trust-blue);}
.field .hint{font-size:12px;color:var(--muted);margin-top:5px;}
.field .error{font-size:12px;color:var(--coral);margin-top:4px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-primary{background:#EEBE15;color:#15281F;}.btn-primary:hover{opacity:.9;}
.btn-ghost{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-ghost:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
</style>
</head>
<body>
@php
$user    = auth()->user();
$profile = $user->tutorProfile;
$isApproved = $profile?->is_approved ?? false;
$pendingBookings = $user->bookingsAsTutor()->where('status','pending')->count();
$unreadNotifs = $user->unreadNotifications()->count();
@endphp
<div class="layout" x-data="{ sidebarOpen: false, isSeries: false }">
<div class="mob-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen=false"></div>

{{-- SIDEBAR (same as index view) --}}
<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div><div class="sb-logo-text">Mwalimu</div><div class="sb-logo-sub">Tutor Portal</div></div>
    </div>
    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <a href="{{ route('tutor.dashboard') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </a>
            <a href="{{ route('tutor.subjects') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                My Subjects
            </a>
        </div>
        @if($isApproved)
        <div class="sb-section">
            <div class="sb-label">Group Classes</div>
            <a href="{{ route('tutor.group-sessions.index') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                My Group Sessions
            </a>
            <a href="{{ route('tutor.group-sessions.create') }}" class="sb-item active">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Create Session
            </a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Examiner</div>
            <a href="{{ route('examiner.dashboard') }}" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Examiner Clinics
            </a>
        </div>
        @endif
        <div class="sb-section">
            <div class="sb-label">Finance</div>
            <a href="{{ route('tutor.dashboard') }}?tab=earnings" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Earnings &amp; Wallet
            </a>
        </div>
        <div class="sb-section">
            <div class="sb-label">Account</div>
            <a href="{{ route('tutor.dashboard') }}?tab=profile" class="sb-item">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile Settings
            </a>
        </div>
    </nav>
    <div class="sb-footer">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div style="flex:1;min-width:0;"><div class="sb-uname">{{ $user->name }}</div><div class="sb-urole">Tutor</div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sb-logout" title="Logout"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="mob-toggle" @click="sidebarOpen=!sidebarOpen"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <span class="page-title">Create Group Session</span>
        </div>
        <a href="{{ route('tutor.group-sessions.index') }}" class="btn btn-ghost" style="padding:7px 16px;font-size:13px;">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back
        </a>
    </div>

    <div class="content">
        @if($errors->any())
        <div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:14px 18px;border-radius:12px;margin-bottom:20px;">
            <p style="font-weight:700;margin-bottom:6px;"><i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:</p>
            <ul style="padding-left:18px;font-size:13px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div style="max-width:700px;">
            <form method="POST" action="{{ route('tutor.group-sessions.store') }}">
                @csrf

                {{-- SESSION BASICS --}}
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);padding:24px;margin-bottom:20px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--hair);">
                        <i class="fa-solid fa-circle-info fa-sm" style="color:#0A64BC;margin-right:7px;"></i>Session Details
                    </h2>

                    <div class="field">
                        <label>Subject <span class="req">*</span></label>
                        <select name="subject_id" required>
                            <option value="">— Select a subject —</option>
                            @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')<p class="error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label>Session Title <span class="req">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Algebra Made Easy – Form 4" required maxlength="200">
                        <p class="hint">Be specific — this is what students see when browsing sessions.</p>
                        @error('title')<p class="error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label>Description <span class="req">*</span></label>
                        <textarea name="description" rows="4" placeholder="What will students learn? Who is this session for?" required maxlength="2000" style="resize:vertical;">{{ old('description') }}</textarea>
                        @error('description')<p class="error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label>Topics Covered</label>
                        <input type="text" name="topics_covered" value="{{ old('topics_covered') }}" placeholder="e.g. Quadratics, Surds, Algebra (comma-separated)">
                        <p class="hint">Comma-separated list of topics. Shown as chips on the detail page.</p>
                        @error('topics_covered')<p class="error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- SCHEDULE --}}
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);padding:24px;margin-bottom:20px;">
                    <h2 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--hair);">
                        <i class="fa-regular fa-calendar fa-sm" style="color:#0A64BC;margin-right:7px;"></i>Schedule & Pricing
                    </h2>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="form-2col">
                        <div class="field" style="margin-bottom:0;">
                            <label>Start Date & Time <span class="req">*</span></label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required>
                            @error('scheduled_at')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <label>Duration (minutes) <span class="req">*</span></label>
                            <select name="duration_minutes" required>
                                @foreach([30,45,60,90,120,180,240] as $d)
                                <option value="{{ $d }}" {{ old('duration_minutes', 60) == $d ? 'selected' : '' }}>{{ $d }} min</option>
                                @endforeach
                            </select>
                            @error('duration_minutes')<p class="error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <style>.form-2col{grid-template-columns:1fr!important;}@media(min-width:540px){.form-2col{grid-template-columns:1fr 1fr!important;}}</style>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;" class="form-2col">
                        <div class="field" style="margin-bottom:0;">
                            <label>Price per Seat (KSh) <span class="req">*</span></label>
                            <input type="number" name="price" value="{{ old('price', 500) }}" min="0" step="50" required>
                            @error('price')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <label>Max Students <span class="req">*</span></label>
                            <input type="number" name="max_students" value="{{ old('max_students', 20) }}" min="2" max="100" required>
                            <p class="hint">Up to 100 per session.</p>
                            @error('max_students')<p class="error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- SERIES TOGGLE --}}
                <div style="background:#fff;border-radius:16px;border:1px solid var(--hair);padding:24px;margin-bottom:24px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                        <div>
                            <h2 style="font-family:'Fraunces',serif;font-size:17px;color:var(--ink);margin-bottom:4px;">
                                <i class="fa-solid fa-layer-group fa-sm" style="color:#EEBE15;margin-right:7px;"></i>Multi-Session Series
                            </h2>
                            <p style="font-size:13px;color:var(--muted);">Create a recurring cohort (e.g. 6-week series). Students enroll per session.</p>
                        </div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_series" value="1" x-model="isSeries" style="width:18px;height:18px;accent-color:#0A64BC;" {{ old('is_series') ? 'checked' : '' }}>
                            <span style="font-size:13px;font-weight:600;color:var(--ink);">Enable series</span>
                        </label>
                    </div>

                    <div x-show="isSeries" x-cloak style="margin-top:18px;padding-top:18px;border-top:1px solid var(--hair);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="form-2col">
                            <div class="field" style="margin-bottom:0;">
                                <label>Number of Sessions</label>
                                <input type="number" name="series_count" value="{{ old('series_count', 4) }}" min="2" max="12" x-bind:required="isSeries">
                                <p class="hint">2–12 sessions per series.</p>
                                @error('series_count')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field" style="margin-bottom:0;">
                                <label>Session Interval</label>
                                <select name="series_interval">
                                    <option value="weekly" {{ old('series_interval','weekly') === 'weekly' ? 'selected' : '' }}>Weekly (every 7 days)</option>
                                    <option value="daily" {{ old('series_interval') === 'daily' ? 'selected' : '' }}>Daily (every day)</option>
                                </select>
                                <p class="hint">Subsequent sessions are spaced from the start date.</p>
                                @error('series_interval')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div style="margin-top:12px;padding:12px 14px;background:rgba(212,160,23,.07);border-radius:10px;border:1px solid rgba(212,160,23,.2);">
                            <p style="font-size:13px;color:#B7870A;"><i class="fa-solid fa-circle-info fa-xs" style="margin-right:6px;"></i>Each session in the series will be created with the same title, subject, duration, and price. Dates auto-increment from the start date.</p>
                        </div>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div style="display:flex;gap:12px;align-items:center;">
                    <button type="submit" class="btn btn-primary" style="padding:13px 32px;font-size:15px;">
                        <i class="fa-solid fa-check"></i>
                        <span x-text="isSeries ? 'Create Series' : 'Create Session'">Create Session</span>
                    </button>
                    <a href="{{ route('tutor.group-sessions.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>{{-- /content --}}
</div>{{-- /main --}}
</div>{{-- /layout --}}
@livewireScripts
</body>
</html>
