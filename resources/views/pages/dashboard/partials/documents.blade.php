{{-- pages/dashboard/partials/documents.blade.php
 Included by: pages/dashboard/tutor.blade.php
 Variables expected: $profile, $isApproved, $verificationStatus
--}}

<div x-show="tab==='documents'" x-cloak>
    @php
    $docs = $profile?->documents ?? collect();
    $docTypes = [
        'national_id' => ['National ID / Passport', '<i class="fa-solid fa-id-card"></i>', 'Required'],
        'tsc_certificate' => ['TSC Certificate', '<i class="fa-solid fa-scroll"></i>', 'Required'],
        'certificate_of_good_conduct' => ['Certificate of Good Conduct', '<i class="fa-solid fa-shield-halved"></i>', 'Optional'],
        'degree_certificate' => ['Academic Certificate', '<i class="fa-solid fa-graduation-cap"></i>', 'Required'],
        'passport_photo' => ['Passport Photo', '<i class="fa-solid fa-camera"></i>', 'Required'],
    ];

    // Check which required docs are uploaded/approved
    $uploadedTypes = $docs->pluck('type')->toArray();
    $approvedTypes = $docs->where('status', 'approved')->pluck('type')->toArray();
    $requiredTypes = array_keys(array_filter($docTypes, fn($v) => $v[2] === 'Required'));
    $missingTypes = array_diff($requiredTypes, $uploadedTypes);
    $allRequiredUploaded = count($missingTypes) === 0;
    $allRequiredApproved = count(array_diff($requiredTypes, $approvedTypes)) === 0;

    // Subject count
    $subjectCount = $profile?->subjects()->count() ?? 0;
    $needsSubjects = $allRequiredApproved && !$isApproved && ($subjectCount < 1 || $subjectCount > 2);
    $canSubmitForReview = $allRequiredUploaded && $subjectCount >= 1 && $subjectCount <= 2;
    @endphp

    {{-- Header --}}
    <div style="margin-bottom:20px;">
        <h2 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--ink);margin-bottom:6px;">
            Verification Documents
        </h2>
        <p style="font-size:14px;color:var(--muted);">
            Upload your documents and select 1–2 teaching subjects. Our team reviews your profile within 24–48 hours.
        </p>
    </div>

    {{-- Overall status banner --}}
    @if($isApproved)
        <div class="status-banner status-approved" style="margin-bottom:24px;">
            <span style="font-size:24px;"><i class="fa-solid fa-circle-check"></i></span>
            <div>
                <div style="font-weight:700;color:#166534;font-size:15px;">Fully Verified</div>
                <div style="font-size:13px;color:#4ADE80;">
                    All documents approved. Your profile is live and accepting bookings.
                </div>
            </div>
        </div>

    @elseif(!$allRequiredUploaded)
        <div class="status-banner status-pending" style="margin-bottom:24px;">
            <span style="font-size:24px;"><i class="fa-solid fa-file-lines"></i></span>
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
            <span style="font-size:24px;"><i class="fa-solid fa-clock"></i></span>
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
            <span style="font-size:24px;"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
                <div style="font-weight:700;color:#991B1B;font-size:15px;">Action Required</div>
                <div style="font-size:13px;color:#B91C1C;">
                    One or more documents were rejected. Please re-upload them to continue.
                </div>
            </div>
        </div>
    @endif

    {{-- Subject requirement banner --}}
    @if($needsSubjects && !$isApproved)
        <div class="status-banner" style="background:#FEF3C7;border:1px solid #FDE68A;margin-bottom:24px;">
            <span style="font-size:22px;flex-shrink:0;"><i class="fa-solid fa-book-open"></i></span>
            <div style="flex:1;">
                <div style="font-weight:700;color:#92400E;font-size:15px;">Select Your Teaching Subjects</div>
                <div style="font-size:13px;color:#78350F;margin-top:3px;line-height:1.6;">
                    @if($subjectCount === 0)
                        Your documents look good! Before your profile can go live, you must select <strong>1 to 2 subjects</strong> you intend to teach.
                    @else
                        You have {{ $subjectCount }} subjects selected — the maximum allowed is 2. Please remove one.
                    @endif
                </div>
                <a href="{{ route('tutor.subjects') }}"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:8px 16px;background:#D97706;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-arrow-right"></i>
                    Go to My Subjects
                </a>
            </div>
        </div>
    @endif

    {{-- Requirements checklist --}}
    @if(!$isApproved)
        <div class="card card-p" style="margin-bottom:20px;">
            <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em;">
                <i class="fa-solid fa-list-check"></i> Requirements before review
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach([
                    ['4 required documents uploaded', $allRequiredUploaded, route('tutor.dashboard').'?tab=documents'],
                    ['1–2 teaching subjects selected', $subjectCount >= 1 && $subjectCount <= 2, route('tutor.subjects')],
                ] as [$label, $done, $link])
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:22px;height:22px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                             background:{{ $done ? 'rgba(16,185,129,.15)' : 'rgba(232,82,42,.1)' }};">
                            <i class="fa-solid {{ $done ? 'fa-check' : 'fa-xmark' }}"
                               style="font-size:12px;color:{{ $done ? '#059669' : '#E8522A' }};"></i>
                        </div>
                        <span style="font-size:13px;color:{{ $done ? '#059669' : 'var(--ink)' }};font-weight:{{ $done ? '500' : '600' }};">
                            {{ $label }}
                            @if(!$done)
                                <a href="{{ $link }}" style="color:var(--trust-blue);font-size:12px;font-weight:600;margin-left:6px;">
                                    Fix this
                                </a>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Progress stepper --}}
    <div class="card card-p" style="margin-bottom:20px;">
        <div style="display:flex;gap:0;overflow:hidden;border-radius:10px;border:1px solid var(--hair);">
            @foreach([['1','Upload Docs'],['2','Under Review'],['3','Approved'],['4','Go Live']] as $i => [$num,$step])
                @php
                $stepDone = ($isApproved && $i < 4)
                    || (!$isApproved && $verificationStatus === 'under_review' && $i < 2)
                    || (!$isApproved && $verificationStatus === 'no_documents' && $i < 1);
                $stepActive = ($isApproved && $i === 3)
                    || (!$isApproved && $verificationStatus === 'under_review' && $i === 1)
                    || (!$isApproved && $verificationStatus === 'no_documents' && $i === 0)
                    || (!$isApproved && !$allRequiredUploaded && $i === 0);
                @endphp
                <div style="flex:1;padding:12px 8px;text-align:center;font-size:12px;font-weight:600;
                     background:{{ $stepDone ? 'var(--mint)' : ($stepActive ? 'var(--trust-blue)' : '#fff') }};
                     color:{{ ($stepDone || $stepActive) ? '#fff' : 'var(--muted)' }};
                     border-right:{{ !$loop->last ? '1px solid var(--hair)' : 'none' }};">
                    {!! $stepDone ? '<i class="fa-solid fa-circle-check"></i>' : $num !!}. {{ $step }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Document cards --}}
    <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($docTypes as $type => [$label, $icon, $req])
        @php
        $doc = $docs->where('type', $type)->first();
        $isMissing = !$doc && in_array($type, $missingTypes);
        $cardClass = $doc
            ? ($doc->status === 'approved' ? 'approved' : ($doc->status === 'rejected' ? 'rejected' : 'uploaded'))
            : '';
        $iconBg = $doc
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
                    <span style="color:{{ $doc && $doc->status === 'approved' ? '#10B981' : ($doc && $doc->status === 'rejected' ? '#E8522A' : '#0A64BC') }};font-size:24px;">
                        {!! $icon !!}
                    </span>
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
                            <i class="fa-solid fa-file"></i> {{ $doc->original_name ?? 'Uploaded file' }}
                            &nbsp;·&nbsp;
                            {{ $doc->created_at->format('M j, Y') }}
                        </div>
                        @if($doc->admin_note)
                            <div style="font-size:12px;color:var(--coral);margin-top:4px;font-weight:500;">
                                <i class="fa-solid fa-comments"></i> Admin: {{ $doc->admin_note }}
                            </div>
                        @endif
                    @else
                        <div style="font-size:12px;
                             color:{{ $isMissing ? 'var(--coral)' : 'var(--muted)' }};
                             font-weight:{{ $isMissing ? '600' : '400' }};">
                            @if($isMissing)
                                <i class="fa-solid fa-triangle-exclamation"></i> Required — please upload this document
                            @else
                                No document uploaded yet
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: badge + actions --}}
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($doc)
                    @php $bc = ['pending'=>'bdg-amber','approved'=>'bdg-green','rejected'=>'bdg-red'][$doc->status] ?? 'bdg-gray'; @endphp
                    <span class="bdg {{ $bc }}">{{ ucfirst($doc->status) }}</span>
                    <button type="button"
                            onclick="viewDocPopup('{{ route('tutor.documents.view', $doc->id) }}', {{ $doc->file_path && str_ends_with(strtolower($doc->file_path), '.pdf') ? 'true' : 'false' }}, '{{ addslashes($label) }}')"
                            class="btn btn-o btn-sm">
                        <i class="fa-solid fa-eye"></i> View
                    </button>
                @endif

                @if(!$doc || $doc->status !== 'approved')
                    <form method="POST"
                          action="{{ route('tutor.documents.upload') }}"
                          enctype="multipart/form-data"
                          style="display:flex;align-items:center;">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="_redirect" value="{{ route('tutor.dashboard') }}?tab=documents">
                        <label style="display:inline-flex;align-items:center;gap:6px;
                               padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;
                               cursor:pointer;transition:all .15s;
                               border:1.5px solid {{ $isMissing ? 'var(--coral)' : 'var(--hair)' }};
                               background:{{ $isMissing ? 'var(--coral)' : '#fff' }};
                               color:{{ $isMissing ? '#fff' : 'inherit' }};"
                               onmouseover="this.style.opacity='.85'"
                               onmouseout="this.style.opacity='1'">
                            <i class="fa-solid fa-upload"></i>
                            {{ $doc ? 'Re-upload' : ($isMissing ? 'Upload Now' : 'Upload') }}
                            <input type="file"
                                   name="document"
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   style="display:none;"
                                   onchange="this.closest('form').submit()">
                        </label>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
    </div>

    {{-- Accepted formats notice --}}
    <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);
                border-radius:14px;padding:16px 20px;">
        <p style="font-size:13px;font-weight:700;color:var(--trust-blue);margin-bottom:6px;">
            <i class="fa-solid fa-circle-info"></i> Accepted formats
        </p>
        <p style="font-size:13px;color:var(--muted);line-height:1.7;">
            JPG, PNG or PDF &middot; Max 5 MB per file &middot; Must be clear and legible &middot;
            National ID must show both sides
        </p>
    </div>
</div>
