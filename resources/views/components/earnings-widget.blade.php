<div class="card shadow-sm mb-4" style="border:1px solid var(--hair,rgba(21,40,31,.1));">
    <div class="card-header text-white" style="background:linear-gradient(135deg,#10B981,#059669);border:none;">
        <h5 class="mb-0" style="font-weight:700;color:#fff;font-size:16px;">
            <svg width="18" height="18" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:8px;" stroke="currentColor" stroke-width="2" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            My Earnings
        </h5>
    </div>
    <div class="card-body" style="padding:20px;">
        <div class="row text-center mb-3">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 rounded" style="background:rgba(16,185,129,.05);border:1px solid rgba(16,185,129,.15);">
                    <h6 class="text-muted small mb-1" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Total Earnings</h6>
                    <h3 class="mb-0" style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:#10B981;">KSh {{ number_format($earnings['total_earnings'], 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 rounded" style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);">
                    <h6 class="text-muted small mb-1" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Last 30 Days</h6>
                    <h3 class="mb-0" style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:#0A64BC;">KSh {{ number_format($earnings['total_earnings_30d'], 2) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background:rgba(212,160,23,.05);border:1px solid rgba(212,160,23,.15);">
                    <h6 class="text-muted small mb-1" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Total Sessions</h6>
                    <h3 class="mb-0" style="font-family:'Fraunces',serif;font-size:26px;font-weight:700;color:#EEBE15;">{{ $earnings['total_sessions_count'] }}</h3>
                </div>
            </div>
        </div>

        <hr style="border-color:rgba(21,40,31,.08);">

        <h6 class="mb-3" style="font-size:13px;font-weight:700;color:#15281F;">
            <svg width="16" height="16" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Earnings Breakdown
        </h6>
        
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="font-size:13.5px;color:#15281F;">
                    <svg width="16" height="16" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;color:#0A64BC;" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <strong>1-on-1 Sessions</strong>
                    <!-- <span class="badge ms-2" style="background:rgba(16,185,129,.15);color:#10B981;font-size:10px;padding:3px 8px;border-radius:6px;">75% rate</span> -->
                </div>
                <strong style="color:#10B981;font-size:14px;">KSh {{ number_format($earnings['booking_earnings'], 2) }}</strong>
            </div>
            <div class="progress" style="height:8px;background:rgba(21,40,31,.06);border-radius:999px;">
                @php
                    $bookingPercentage = $earnings['total_earnings'] > 0 
                        ? ($earnings['booking_earnings'] / $earnings['total_earnings'] * 100) 
                        : 0;
                @endphp
                <div class="progress-bar" role="progressbar" style="width:{{ $bookingPercentage }}%;background:#0A64BC;border-radius:999px;"></div>
            </div>
            <small class="text-muted" style="font-size:12px;">{{ $earnings['booking_sessions_count'] }} sessions</small>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="font-size:13.5px;color:#15281F;">
                    <svg width="16" height="16" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;color:#EEBE15;" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <strong>Exam Clinics</strong>
                    <!-- <span class="badge ms-2" style="background:rgba(212,160,23,.15);color:#EEBE15;font-size:10px;padding:3px 8px;border-radius:6px;">40% rate</span> -->
                </div>
                <strong style="color:#10B981;font-size:14px;">KSh {{ number_format($earnings['clinic_earnings'], 2) }}</strong>
            </div>
            <div class="progress" style="height:8px;background:rgba(21,40,31,.06);border-radius:999px;">
                @php
                    $clinicPercentage = $earnings['total_earnings'] > 0 
                        ? ($earnings['clinic_earnings'] / $earnings['total_earnings'] * 100) 
                        : 0;
                @endphp
                <div class="progress-bar" role="progressbar" style="width:{{ $clinicPercentage }}%;background:#EEBE15;border-radius:999px;"></div>
            </div>
            <small class="text-muted" style="font-size:12px;">{{ $earnings['clinic_sessions_count'] }} sessions</small>
        </div>  
    </div>
</div>
