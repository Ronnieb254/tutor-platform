<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <title>Tutor Services Agreement - Mwalimu</title>
    <style>
        .contract-container { max-width: 900px; margin: 0 auto; padding: 40px 24px; }
        .contract-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 24px rgba(21,40,31,.08); }
        .contract-content { max-height: 500px; overflow-y: auto; border: 2px solid rgba(21,40,31,.1); border-radius: 12px; padding: 24px; margin-bottom: 24px; background: rgba(251,245,230,.3); }
        .contract-section { margin-bottom: 24px; }
        .contract-section h3 { font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .contract-section p { line-height: 1.8; color: var(--muted); margin-bottom: 12px; }
        .accept-box { background: rgba(10,100,188,.05); border: 2px solid var(--trust-blue); border-radius: 16px; padding: 24px; margin-top: 24px; }
        .checkbox-label { display: flex; align-items: flex-start; gap: 12px; cursor: pointer; }
        .checkbox-label input[type="checkbox"] { width: 20px; height: 20px; margin-top: 2px; cursor: pointer; flex-shrink: 0; }
        .btn-accept { width: 100%; padding: 16px; border-radius: 999px; background: var(--trust-blue); color: #fff; font-weight: 700; font-size: 16px; border: none; cursor: pointer; transition: all .2s; margin-top: 16px; }
        .btn-accept:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-accept:not(:disabled):hover { background: #120A8F; transform: translateY(-2px); }
    </style>
</head>
<body style="background: var(--cream);">

<div class="contract-container">
    <div class="contract-card">
        <div style="text-align: center; margin-bottom: 32px;">
            <i class="fa-solid fa-file-contract" style="font-size: 48px; color: var(--trust-blue); margin-bottom: 16px;"></i>
            <h1 style="font-family: 'Fraunces', serif; font-size: 32px; color: var(--ink); margin-bottom: 8px;">Tutor Services Agreement</h1>
            <p style="color: var(--muted);">Please read carefully before accepting</p>
        </div>

        @if(session('error'))
        <div style="background: rgba(239,68,68,.1); border-left: 3px solid #EF4444; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <p style="color: #991B1B; margin: 0;"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</p>
        </div>
        @endif

        <div class="contract-content">
            <div class="contract-section">
                <h3>ELECTRONIC TUTOR SERVICES AGREEMENT</h3>
                <p><strong>Between:</strong> Mwalimu (Oval Desk Ventures, BN-RRSKQQOZ) and You ({{ auth()->user()->name }})</p>
            </div>

            <div class="contract-section">
                <h3>1. PURPOSE AND SCOPE</h3>
                <p>This Agreement establishes the terms governing your engagement as a tutor on the Mwalimu platform, including professional obligations, compensation, conduct, learner safeguarding, confidentiality, and platform use.</p>
            </div>

            <div class="contract-section">
                <h3>2. NATURE OF RELATIONSHIP</h3>
                <p>You will provide tuition as an independent service provider, not as an employee of Mwalimu. You are responsible for your own tax obligations, statutory registrations, insurance, and business expenses.</p>
            </div>

            <div class="contract-section">
                <h3>3. TUTOR REQUIREMENTS</h3>
                <p>You warrant that all information and documents submitted are genuine, accurate, and complete. You must promptly notify Mwalimu of any material change to your qualifications, professional status, or eligibility.</p>
            </div>

            <div class="contract-section">
                <h3>4. LESSON DELIVERY & PROFESSIONAL CONDUCT</h3>
                <p><strong>You agree to:</strong></p>
                <ul style="line-height: 1.8; color: var(--muted); margin-left: 20px;">
                    <li>Attend booked lessons punctually and deliver quality instruction</li>
                    <li>Act honestly, respectfully, and professionally</li>
                    <li>Maintain appropriate professional boundaries with all learners</li>
                    <li>Never discriminate, harass, or engage in inappropriate conduct</li>
                    <li>Not encourage cheating, plagiarism, or examination malpractice</li>
                    <li>Use learner information only for authorized educational purposes</li>
                </ul>
            </div>

            <div class="contract-section">
                <h3>5. CHILD SAFEGUARDING (CRITICAL)</h3>
                <p><strong>For learners under 18, you must:</strong></p>
                <ul style="line-height: 1.8; color: var(--muted); margin-left: 20px;">
                    <li>Communicate only through approved Mwalimu channels</li>
                    <li>Maintain strict professional boundaries at all times</li>
                    <li>NEVER solicit, exchange, or distribute inappropriate material</li>
                    <li>NEVER engage in grooming, harassment, exploitation, or threats</li>
                    <li>NEVER request unnecessary personal info, photos, money, or gifts</li>
                    <li>NEVER arrange in-person meetings without parent/guardian approval</li>
                    <li>Report suspected abuse or safeguarding concerns immediately</li>
                </ul>
                <p style="color: #991B1B; font-weight: 700;">⚠️ Violation of safeguarding rules will result in immediate account termination and reporting to authorities.</p>
            </div>

            <div class="contract-section">
                <h3>6. FEES, COMMISSION & PAYMENTS</h3>
                <p>Mwalimu will collect payment from learners and remit your compensation after deducting the disclosed platform commission and transaction charges. Payment details and schedules are specified in Schedule A of the full contract.</p>
                <p><strong>You must not:</strong></p>
                <ul style="line-height: 1.8; color: var(--muted); margin-left: 20px;">
                    <li>Collect additional fees from learners for platform bookings</li>
                    <li>Misrepresent lesson completion or collude to create fraudulent transactions</li>
                    <li>Use confidential learner information to circumvent platform payments</li>
                </ul>
            </div>

            <div class="contract-section">
                <h3>7. PRIVACY & DATA PROTECTION</h3>
                <p>You must comply with the Data Protection Act, 2019 and Mwalimu's privacy policies. Keep all learner information confidential and use it only for authorized educational purposes. Never sell, disclose, or commercially exploit learner data.</p>
            </div>

            <div class="contract-section">
                <h3>8. CANCELLATIONS & ATTENDANCE</h3>
                <p>Notify Mwalimu and the learner immediately if unable to attend a confirmed lesson. Repeated cancellations, late arrivals, or no-shows may result in suspension or account termination.</p>
            </div>

            <div class="contract-section">
                <h3>9. SUSPENSION & TERMINATION</h3>
                <p>Either party may terminate this Agreement with 14 days' written notice. Mwalimu may immediately suspend or terminate your account for:</p>
                <ul style="line-height: 1.8; color: var(--muted); margin-left: 20px;">
                    <li>Safeguarding concerns or serious misconduct</li>
                    <li>Fraud or falsification of credentials</li>
                    <li>Repeated learner complaints or poor service</li>
                    <li>Breach of confidentiality or data protection</li>
                </ul>
            </div>

            <div class="contract-section">
                <h3>10. DISPUTE RESOLUTION & GOVERNING LAW</h3>
                <p>Disputes shall first be resolved through good-faith discussions, then mediation if necessary. This Agreement is governed by the laws of Kenya.</p>
            </div>

            <div class="contract-section">
                <h3>11. FULL CONTRACT</h3>
                <p>This summary covers key terms. The complete Tutor Services Agreement (emailed to you) contains full details of all terms, schedules, and conditions.</p>
                <p><strong>Download Full Contract:</strong> <a href="{{ route('download-tutor-contract') }}" style="color: var(--trust-blue); text-decoration: underline;" target="_blank"><i class="fa-solid fa-file-pdf"></i> Tutor-Services-Agreement.pdf</a></p>
            </div>
        </div>

        <form method="POST" action="{{ route('accept-tutor-contract') }}" id="contractForm">
            @csrf
            <div class="accept-box">
                <label class="checkbox-label">
                    <input type="checkbox" name="accept_contract" id="acceptCheckbox" required>
                    <span style="line-height: 1.6; color: var(--ink);">
                        <strong>I confirm that:</strong><br>
                        • I have read and understood the full Tutor Services Agreement<br>
                        • I agree to comply with all professional conduct, safeguarding, privacy, and academic integrity requirements<br>
                        • I understand the payment terms, platform commission, and cancellation rules<br>
                        • All qualifications and information I provided are accurate<br>
                        • I will promptly notify Mwalimu of any changes affecting my eligibility<br>
                        • I understand this is a legally binding agreement governed by Kenyan law
                    </span>
                </label>

                <button type="submit" class="btn-accept" id="acceptButton" disabled>
                    <i class="fa-solid fa-check-circle"></i> I Accept the Agreement & Start Teaching
                </button>

                <p style="text-align: center; font-size: 12px; color: var(--muted); margin-top: 12px;">
                    By clicking above, you electronically sign and accept this agreement as of {{ now()->format('F d, Y') }}
                </p>
            </div>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--muted); font-size: 14px; text-decoration: underline;">
                Sign Out Instead
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </div>
</div>

<script>
document.getElementById('acceptCheckbox').addEventListener('change', function() {
    document.getElementById('acceptButton').disabled = !this.checked;
});
</script>

</body>
</html>
