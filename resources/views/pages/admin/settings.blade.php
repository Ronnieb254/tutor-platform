@extends('layouts.admin')
@section('title', 'Platform Settings')

@section('content')
@php
use App\Models\PlatformSetting;
@endphp

<div class="page-header">
    <div>
        <h1>Platform Settings</h1>
        <p>Configure global platform behaviour, fees, and integrations</p>
    </div>
</div>

<form method="POST" action="/admin/settings">
@csrf
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- General --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            🌐 General
        </h3>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Platform Name</label>
                <input type="text" name="platform_name" class="form-input" value="{{ PlatformSetting::get('platform_name','Mwalimu') }}">
            </div>
            <div>
                <label class="form-label">Tagline</label>
                <input type="text" name="platform_tagline" class="form-input" value="{{ PlatformSetting::get('platform_tagline','Find verified tutors in Kenya') }}">
            </div>
            <div>
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-input" value="{{ PlatformSetting::get('contact_email','hello@Mwalimu.co.ke') }}">
            </div>
        </div>
    </div>

    {{-- Payments --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            💳 Payments
        </h3>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Commission Rate (%)</label>
                <input type="number" name="commission_rate" class="form-input" step="0.01" min="0" max="1"
                       value="{{ PlatformSetting::get('commission_rate','0.15') }}"
                       placeholder="0.15 = 15%">
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">Enter as decimal: 0.15 = 15% platform fee</p>
            </div>
            <div>
                <label class="form-label">Currency Code</label>
                <input type="text" name="currency" class="form-input" value="{{ PlatformSetting::get('currency','KES') }}">
            </div>
            <div>
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-input" value="{{ PlatformSetting::get('currency_symbol','KSh') }}">
            </div>
            <div>
                <label class="form-label">Minimum Hourly Rate (KSh)</label>
                <input type="number" name="min_hourly_rate" class="form-input" value="{{ PlatformSetting::get('min_hourly_rate','500') }}">
            </div>
        </div>
    </div>

    {{-- Paystack --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            💰 Paystack Integration
        </h3>
        <div style="background:rgba(212,160,23,0.08);border:1px solid rgba(212,160,23,0.3);border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#92400E;">
            ⚠ These keys are stored in your <code style="background:rgba(0,0,0,0.06);padding:1px 6px;border-radius:4px;">.env</code> file. Update them there directly for security.
        </div>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Public Key (from .env)</label>
                <input type="text" class="form-input" value="{{ config('paystack.public_key') ? substr(config('paystack.public_key'),0,12).'••••••••••' : 'Not configured' }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div>
                <label class="form-label">Secret Key (from .env)</label>
                <input type="text" class="form-input" value="sk_••••••••••••••••" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div>
                <label class="form-label">Webhook URL</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" class="form-input" value="{{ url('/api/paystack/webhook') }}" readonly style="background:#F4F6F4;font-size:12px;">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/paystack/webhook') }}')" class="btn btn-outline btn-sm" style="white-space:nowrap;">Copy</button>
                </div>
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">Paste this URL in your Paystack dashboard → Settings → Webhooks</p>
            </div>
        </div>
    </div>

    {{-- Jitsi --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            🎥 Jitsi Live Sessions
        </h3>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Jitsi Domain</label>
                <input type="text" class="form-input" value="{{ config('jitsi.domain','meet.jit.si') }}" disabled style="background:#F4F6F4;color:var(--muted);">
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">Configured in .env as JITSI_DOMAIN</p>
            </div>
            <div>
                <label class="form-label">App ID</label>
                <input type="text" class="form-input" value="{{ config('jitsi.app_id') ? '••••••' : 'Not set (using public Jitsi)' }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:12px;padding:12px 14px;font-size:13px;color:#065F46;">
                ✓ Sessions use <strong>meet.jit.si</strong> (public) by default. Set JITSI_APP_ID and JITSI_APP_SECRET in .env for a private server with JWT auth.
            </div>
        </div>
    </div>

    {{-- Session Durations info --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            ⏱ Session Duration Pricing
        </h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6;">Session prices are calculated automatically from each tutor's hourly rate using these fixed multipliers:</p>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 0;border-bottom:1px solid var(--hairline);">Duration</th>
                    <th style="text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 0;border-bottom:1px solid var(--hairline);">Multiplier</th>
                    <th style="text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 0;border-bottom:1px solid var(--hairline);">Example (KSh 2,000/hr)</th>
                </tr>
            </thead>
            <tbody>
                @foreach([[15,'25%','KSh 500'],[30,'50%','KSh 1,000'],[45,'75%','KSh 1,500'],[60,'100%','KSh 2,000']] as [$m,$pct,$ex])
                <tr style="border-bottom:1px solid rgba(21,40,31,0.05);">
                    <td style="padding:10px 0;font-weight:600;">{{ $m }} min</td>
                    <td style="padding:10px 0;color:var(--trust-blue);font-weight:600;">{{ $pct }}</td>
                    <td style="padding:10px 0;color:var(--muted);">{{ $ex }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Maintenance --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            🔧 Maintenance
        </h3>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:#F4F6F4;border-radius:12px;">
                <div>
                    <div style="font-weight:600;font-size:13.5px;">Clear Application Cache</div>
                    <div style="font-size:12px;color:var(--muted);">config, route, view caches</div>
                </div>
                <a href="/admin/maintenance/clear-cache" class="btn btn-outline btn-sm">Clear Cache</a>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:#F4F6F4;border-radius:12px;">
                <div>
                    <div style="font-weight:600;font-size:13.5px;">Storage Link</div>
                    <div style="font-size:12px;color:var(--muted);">Refresh public storage symlink</div>
                </div>
                <a href="/admin/maintenance/storage-link" class="btn btn-outline btn-sm">Re-link</a>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:#FEF2F2;border-radius:12px;border:1px solid #FECACA;">
                <div>
                    <div style="font-weight:600;font-size:13.5px;color:#991B1B;">Maintenance Mode</div>
                    <div style="font-size:12px;color:#B91C1C;">Temporarily take the site offline</div>
                </div>
                <a href="/admin/maintenance/down" class="btn btn-danger btn-sm" onclick="return confirm('Put site in maintenance mode?')">Enable</a>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:20px;display:flex;justify-content:flex-end;">
    <button type="submit" class="btn btn-primary" style="padding:12px 36px;font-size:15px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Save Settings
    </button>
</div>
</form>
@endsection
