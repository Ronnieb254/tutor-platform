{{-- ═══════════════════════════════════════════════════════════
     📁 pages/dashboard/partials/documents.blade.php
     Included by: pages/dashboard/tutor.blade.php
     Variables expected: $profile, $isApproved, $verificationStatus
═══════════════════════════════════════════════════════════ --}}

<div x-show="tab==='documents'" x-cloak>
    @php
    $docs = $profile?->documents ?? collect();
    $docTypes = [
        'national_id'        => ['National ID / Passport', '🪪', 'Required'],
        'tsc_certificate'    => ['TSC Certificate',        '📜', 'Required'],
        'degree_certificate' => ['Academic Certificate',   '🎓', 'Required'],
        'passport_photo'     => ['Passport Photo',         '📸', 'Required'],
    ];

    // Check which required docs are uploaded
    $uploadedTypes       = $docs->pluck('type')->toArray();
    $requiredTypes       = array_keys($docTypes);
    $missingTypes        = array_diff($requiredTypes, $uploadedTypes);
    $allRequiredUploaded = count($missingTypes) === 0;
    @endphp

    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div style="margin-bottom:20px;">
        <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);margin-bottom:6px;">
            Verification Documents
        </h2>
        <p style="font-size:14px;color:var(--muted);">
            Upload your documents below. Our team reviews them within 24–48 hours.
            Once <strong>all 4</strong> required documents are approved, your profile goes live.
        </p>
    </div>

    {{-- ── Overall status banner ───────────────────────────────── --}}
    @if($isApproved)
        <div class="status-banner status-approved" style="margin-bottom:24px;">
            <span style="font-size:24px;">✅</span>
            <div>
                <div style="font-weight:700;color:#166534;font-size:15px;">Fully Verified</div>
                <div style="font-size:13px;color:#4ADE80;">
                    All documents approved. Your profile is live and accepting bookings.
                </div>
            </div>
        </div>

    @elseif(!$allRequiredUploaded)
        <div class="status-banner status-pending" style="margin-bottom:24px;">
            <span style="font-size:24px;">📄</span>
            <div>
                <div style="font-weight:700;color:#854D0E;font-size:15px;">Upload All Required Documents</div>
                <div style="font-size:13px;color:#92400E;">
                    You must upload <strong>all 4 required documents</strong> before your profile can be reviewed.
                    <span style="font-weight:700;color:var(--coral);">
                        {{ count($missingTypes) }} remaining
                    </span>
                </div>
            </div>
        </div>

    @elseif($verificationStatus === 'under_review')
        <div class="status-banner status-pending" style="margin-bottom:24px;">
            <span style="font-size:24px;">⏳</span>
            <div>
                <div style="font-weight:700;color:#854D0E;font-size:15px;">Under Review</div>
                <div style="font-size:13px;color:#92400E;">
                    Your documents have been submitted and are being reviewed by our team.
                    We'll email you within 24–48 hours.
                </div>
            </div>
        </div>

    @elseif($verificationStatus === 'action_required')
        <div class="status-banner status-rejected" style="margin-bottom:24px;">
            <span style="font-size:24px;">⚠️</span>
            <div>
                <div style="font-weight:700;color:#991B1B;font-size:15px;">Action Required</div>
                <div style="font-size:13px;color:#B91C1C;">
                    One or more documents were rejected. Please re-upload them to continue.
                </div>
            </div>
        </div>
    @endif

    {{-- ── Progress stepper ────────────────────────────────────── --}}
    <div class="card card-p" style="margin-bottom:20px;">
        <div style="display:flex;gap:0;overflow:hidden;border-radius:10px;border:1px solid var(--hair);">
            @foreach([['1','Upload Docs'],['2','Under Review'],['3','Approved'],['4','Go Live']] as $i => [$num,$step])
            @php
                $stepDone   = ($isApproved && $i < 4)
                           || (!$isApproved && $verificationStatus === 'under_review'  && $i < 2)
                           || (!$isApproved && $verificationStatus === 'no_documents'  && $i < 1);
                $stepActive = ($isApproved && $i === 3)
                           || (!$isApproved && $verificationStatus === 'under_review'  && $i === 1)
                           || (!$isApproved && $verificationStatus === 'no_documents'  && $i === 0)
                           || (!$isApproved && !$allRequiredUploaded                    && $i === 0);
            @endphp
            <div style="flex:1;padding:12px 8px;text-align:center;font-size:12px;font-weight:600;
                background:{{ $stepDone ? 'var(--mint)' : ($stepActive ? 'var(--trust-blue)' : '#fff') }};
                color:{{ ($stepDone || $stepActive) ? '#fff' : 'var(--muted)' }};
                border-right:{{ !$loop->last ? '1px solid var(--hair)' : 'none' }};">
                {{ $stepDone ? '✓' : $num }}. {{ $step }}
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Document cards ──────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
        @foreach($docTypes as $type => [$label, $icon, $req])
        @php
            $doc       = $docs->where('type', $type)->first();
            $isMissing = !$doc && in_array($type, $missingTypes);
            $cardClass = $doc
                ? ($doc->status === 'approved' ? 'approved' : ($doc->status === 'rejected' ? 'rejected' : 'uploaded'))
                : '';
            $iconBg    = $doc
                ? ($doc->status === 'approved' ? 'rgba(16,185,129,.15)'
                    : ($doc->status === 'rejected' ? 'rgba(232,82,42,.15)'
                    : 'rgba(10,100,188,.08)'))
                : ($isMissing ? 'rgba(232,82,42,.08)' : 'rgba(10,100,188,.04)');
        @endphp

        <div class="doc-card {{ $cardClass }}"
             style="{{ $isMissing ? 'border-color:var(--coral);border-width:2px;' : '' }}">

            {{-- Left: icon + info --}}
            <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
                <div style="width:52px;height:52px;border-radius:13px;background:{{ $iconBg }};
                            display:flex;align-items:center;justify-content:center;
                            font-size:24px;flex-shrink:0;">
                    {{ $icon }}
                </div>
                <div style="min-width:0;">
                    {{-- Title row --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
                        <span style="font-weight:700;font-size:14px;color:var(--ink);">{{ $label }}</span>
                        <span style="font-size:11px;font-weight:600;
                                     color:{{ $req === 'Required' ? 'var(--coral)' : 'var(--muted)' }};">
                            {{ $req }}
                        </span>
                        @if($isMissing)
                        <span style="font-size:10px;font-weight:700;color:var(--coral);
                                     background:rgba(232,82,42,.1);padding:2px 8px;border-radius:999px;
                                     letter-spacing:.03em;">
                            NOT UPLOADED
                        </span>
                        @endif
                    </div>

                    {{-- Subtitle --}}
                    @if($doc)
                        <div style="font-size:12px;color:var(--muted);">
                            📎 {{ $doc->original_name ?? 'Uploaded file' }}
                            &nbsp;·&nbsp;
                            {{ $doc->created_at->format('M j, Y') }}
                        </div>
                        @if($doc->admin_note)
                        <div style="font-size:12px;color:var(--coral);margin-top:4px;font-weight:500;">
                            💬 Admin: {{ $doc->admin_note }}
                        </div>
                        @endif
                    @else
                        <div style="font-size:12px;
                                    color:{{ $isMissing ? 'var(--coral)' : 'var(--muted)' }};
                                    font-weight:{{ $isMissing ? '600' : '400' }};">
                            {{ $isMissing
                                ? '⚠️ Required — please upload this document'
                                : 'No document uploaded yet' }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: badge + actions --}}
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($doc)
                    @php $bc = ['pending'=>'bdg-amber','approved'=>'bdg-green','rejected'=>'bdg-red'][$doc->status] ?? 'bdg-gray'; @endphp
                    <span class="bdg {{ $bc }}">{{ ucfirst($doc->status) }}</span>
                    <a href="{{ asset('storage/' . $doc->file_path) }}"
                       target="_blank"
                       class="btn btn-o btn-sm">
                        View
                    </a>
                @endif

                <form method="POST"
                      action="{{ route('tutor.documents.upload') }}"
                      enctype="multipart/form-data"
                      style="display:flex;align-items:center;">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <label style="display:inline-flex;align-items:center;gap:6px;
                                  padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;
                                  cursor:pointer;transition:all .15s;
                                  border:1.5px solid {{ $isMissing ? 'var(--coral)' : 'var(--hair)' }};
                                  background:{{ $isMissing ? 'var(--coral)' : '#fff' }};
                                  color:{{ $isMissing ? '#fff' : 'inherit' }};"
                          onmouseover="this.style.opacity='.85'"
                          onmouseout="this.style.opacity='1'">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5">
                            <polyline points="16 16 12 12 8 16"/>
                            <line x1="12" y1="12" x2="12" y2="21"/>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                        </svg>
                        {{ $doc ? 'Re-upload' : ($isMissing ? 'Upload Now' : 'Upload') }}
                        <input type="file"
                               name="document"
                               accept=".jpg,.jpeg,.png,.pdf"
                               style="display:none;"
                               onchange="this.closest('form').submit()">
                    </label>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Accepted formats notice ─────────────────────────────── --}}
    <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);
                border-radius:14px;padding:16px 20px;">
        <p style="font-size:13px;font-weight:700;color:var(--trust-blue);margin-bottom:6px;">
            📋 Accepted formats
        </p>
        <p style="font-size:13px;color:var(--muted);line-height:1.7;">
            JPG, PNG or PDF &middot; Max 5 MB per file &middot; Must be clear and legible &middot;
            National ID must show both sides if applicable
        </p>
    </div>
</div>
