@extends('layouts.admin')
@section('title', 'Suggestion Details')
@section('content')

<div style="max-width:1200px;margin:0 auto;padding:24px;">
    <!-- Header with Back Button -->
    <div style="margin-bottom:28px;">
        <a href="{{ route('admin.group-session-suggestions.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:#0A64BC;text-decoration:none;font-size:14px;font-weight:600;margin-bottom:16px;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Back to Suggestions
        </a>
        <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin:0 0 6px 0;">{{ $suggestion->topic }}</h1>
        <p style="color:var(--muted);font-size:14px;margin:0;">Submitted on {{ $suggestion->created_at->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;">
        <!-- Main Content -->
        <div>
            <!-- Suggestion Details Card -->
            <div class="card" style="margin-bottom:20px;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Suggestion Details</h3>
                </div>
                <div style="padding:20px;">
                    <!-- Topic -->
                    <div style="margin-bottom:24px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Topic</label>
                        <p style="font-size:16px;font-weight:600;color:var(--ink);margin:0;">{{ $suggestion->topic }}</p>
                    </div>

                    <!-- Description -->
                    @if($suggestion->description)
                        <div style="margin-bottom:24px;">
                            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Description</label>
                            <p style="font-size:14px;color:var(--ink);margin:0;line-height:1.6;word-break:break-word;">{{ $suggestion->description }}</p>
                        </div>
                    @endif

                    <!-- Metadata -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;padding-top:24px;border-top:1px solid rgba(21,40,31,.05);">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Submitted Date</label>
                            <p style="font-size:14px;color:var(--ink);margin:0;">{{ $suggestion->created_at->format('M d, Y') }}</p>
                            <p style="font-size:12px;color:var(--muted);margin:6px 0 0 0;">{{ $suggestion->created_at->format('g:i A') }}</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Last Updated</label>
                            <p style="font-size:14px;color:var(--ink);margin:0;">{{ $suggestion->updated_at->format('M d, Y') }}</p>
                            <p style="font-size:12px;color:var(--muted);margin:6px 0 0 0;">{{ $suggestion->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card" style="margin-bottom:20px;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Submitted By</h3>
                </div>
                <div style="padding:20px;">
                    @if($suggestion->user)
                        <div style="display:flex;align-items:center;gap:16px;">
                            <div style="width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg, #0A64BC, #1f40af);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:700;font-size:18px;">
                                {{ strtoupper(substr($suggestion->user->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;">
                                <p style="font-size:14px;font-weight:600;color:var(--ink);margin:0;">{{ $suggestion->user->name }}</p>
                                <p style="font-size:13px;color:var(--muted);margin:4px 0 0 0;">ID: #{{ $suggestion->user->id }}</p>
                                <p style="font-size:13px;color:var(--muted);margin:2px 0 0 0;">{{ $suggestion->user->email }}</p>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:rgba(212,160,23,.1);border-radius:10px;border-left:4px solid #EEBE15;">
                            <span style="font-size:28px;">👤</span>
                            <div>
                                <p style="font-size:14px;font-weight:600;color:var(--ink);margin:0;">Guest User</p>
                                <p style="font-size:13px;color:var(--muted);margin:4px 0 0 0;">Not logged in</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="card">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Contact Information</h3>
                </div>
                <div style="padding:20px;">
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Email Address</label>
                        <p style="font-size:14px;color:var(--ink);margin:0;">
                            @if($suggestion->email)
                                <a href="mailto:{{ $suggestion->email }}" style="color:#0A64BC;text-decoration:none;">{{ $suggestion->email }}</a>
                            @else
                                <span style="color:var(--muted);">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">IP Address</label>
                        <p style="font-size:14px;color:var(--ink);margin:0;">
                            @if($suggestion->ip_address)
                                <code style="background:#f4f6f4;padding:4px 8px;border-radius:6px;font-size:13px;">{{ $suggestion->ip_address }}</code>
                            @else
                                <span style="color:var(--muted);">Not available</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div class="card" style="margin-bottom:20px;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Status</h3>
                </div>
                <div style="padding:20px;">
                    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:14px 16px;text-align:center;margin-bottom:16px;">
                        <div style="display:inline-flex;align-items:center;gap:8px;color:#059669;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                            <span style="font-weight:600;font-size:13px;">Active</span>
                        </div>
                    </div>
                    <p style="font-size:12px;color:var(--muted);margin:0;text-align:center;">This suggestion is visible and available for analysis.</p>
                </div>
            </div>

            <!-- Record Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Record Info</h3>
                </div>
                <div style="padding:20px;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Record ID</label>
                        <code style="background:#f4f6f4;padding:6px 10px;border-radius:6px;font-size:12px;display:block;word-break:break-all;">{{ $suggestion->id }}</code>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Created</label>
                        <p style="font-size:12px;color:var(--ink);margin:0;">{{ $suggestion->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);">
                    <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Actions</h3>
                </div>
                <div style="padding:20px;">
                    <form method="POST" action="{{ route('admin.group-session-suggestions.destroy', $suggestion) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="width:100%;background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);padding:10px;font-weight:600;border-radius:10px;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right:6px;display:inline;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Delete Suggestion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
