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
            Ã°Å¸Å’Â General
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
            Ã°Å¸'Â³ Payments
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

    {{-- Daraja M-Pesa --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            Ã°Å¸"Â± Daraja M-Pesa Integration
        </h3>
        <div style="background:rgba(212,160,23,0.08);border:1px solid rgba(212,160,23,0.3);border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#92400E;">
            Å¡Â  These keys are stored in your <code style="background:rgba(0,0,0,0.06);padding:1px 6px;border-radius:4px;">.env</code> file. Update them there directly for security.
        </div>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Environment</label>
                <input type="text" class="form-input" value="{{ config('daraja.environment', 'sandbox') }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div>
                <label class="form-label">Consumer Key (from .env)</label>
                <input type="text" class="form-input" value="{{ config('daraja.consumer_key') ? substr(config('daraja.consumer_key'),0,8).'â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢' : 'Not configured' }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div>
                <label class="form-label">Business Short Code</label>
                <input type="text" class="form-input" value="{{ config('daraja.shortcode', 'Not configured') }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div>
                <label class="form-label">Callback URL</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" class="form-input" value="{{ url('/api/daraja/callback') }}" readonly style="background:#F4F6F4;font-size:12px;">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/daraja/callback') }}')" class="btn btn-outline btn-sm" style="white-space:nowrap;">Copy</button>
                </div>
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">Set <code>DARAJA_CALLBACK_URL</code> in .env to this URL. Must be a publicly reachable HTTPS endpoint.</p>
            </div>
        </div>
    </div>

    {{-- Jitsi --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            Ã°Å¸Å½Â¥ Jitsi Live Sessions
        </h3>
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="form-label">Jitsi Domain</label>
                <input type="text" class="form-input" value="{{ config('jitsi.domain','meet.jit.si') }}" disabled style="background:#F4F6F4;color:var(--muted);">
                <p style="font-size:12px;color:var(--muted);margin-top:4px;">Configured in .env as JITSI_DOMAIN</p>
            </div>
            <div>
                <label class="form-label">App ID</label>
                <input type="text" class="form-input" value="{{ config('jitsi.app_id') ? 'â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢â‚¬Â¢' : 'Not set (using public Jitsi)' }}" disabled style="background:#F4F6F4;color:var(--muted);">
            </div>
            <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:12px;padding:12px 14px;font-size:13px;color:#065F46;">
                Å“" Sessions use <strong>meet.jit.si</strong> (public) by default. Set JITSI_APP_ID and JITSI_APP_SECRET in .env for a private server with JWT auth.
            </div>
        </div>
    </div>

    {{-- Session Durations info --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span> Session Duration Pricing
        </h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6;">
            Mwalimu uses <strong>fixed platform rates</strong> for all sessions. Tutors do not set their own prices. All rates are inclusive of the platform commission.
        </p>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 12px;border-bottom:1px solid var(--hairline);background:rgba(21,40,31,.02);">Duration</th>
                    <th style="text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 12px;border-bottom:1px solid var(--hairline);background:rgba(21,40,31,.02);">Session Type</th>
                    <th style="text-align:right;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:8px 12px;border-bottom:1px solid var(--hairline);background:rgba(21,40,31,.02);">Price (KSh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\TutorProfile::sessionPrices() as $key => $session)
                <tr style="border-bottom:1px solid rgba(21,40,31,.05);">
                    <td style="padding:11px 12px;font-weight:600;color:var(--ink);">{{ $session['duration'] }} min</td>
                    <td style="padding:11px 12px;color:var(--muted);font-size:13px;">{{ $session['name'] }}</td>
                    <td style="padding:11px 12px;text-align:right;">
                        @if(($session['price'] ?? 0) == 0)
                            <span style="background:rgba(16,185,129,.1);color:#059669;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">FREE</span>
                        @else
                            <span style="font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:var(--trust-blue);">KSh {{ number_format($session['price']) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:12px;color:var(--muted);margin-top:12px;padding-top:12px;border-top:1px solid var(--hairline);">
            <i class="fas fa-info-circle"></i>
            To change session prices, update the <code style="background:#F4F6F4;padding:1px 6px;border-radius:4px;">sessionPrices()</code> method in <code style="background:#F4F6F4;padding:1px 6px;border-radius:4px;">app/Models/TutorProfile.php</code>.
        </p>
    </div>

    {{-- Maintenance --}}
    <div class="card card-p">
        <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
            Ã°Å¸â€Â§ Maintenance
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

@push('styles')
<style>
@media(max-width:767px){
    .settings-section, [class*="settings-grid"]{ display:block !important; }
    .settings-section > *, [class*="settings-grid"] > *{ width:100% !important; }
    /* the inline 1fr 1fr settings grid */
    form > div[style*="grid-template-columns:1fr 1fr"]{
        grid-template-columns:1fr !important;
    }
}
</style>
@endpush
@endsection
