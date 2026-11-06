<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <title>Tutor Services Agreement - Mwalimu</title>
    <style>
        .contract-container { max-width: 900px; margin: 0 auto; padding: 40px 24px; }
        .contract-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 24px rgba(21,40,31,.08); }
        .contract-content { max-height: 700px; overflow-y: auto; border: 2px solid rgba(21,40,31,.1); border-radius: 12px; padding: 24px; margin-bottom: 24px; background: rgba(251,245,230,.3); }
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
            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ Parties Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>ELECTRONIC TUTOR SERVICES AGREEMENT</h3>
                <p style="color:var(--muted);font-size:13px;">Mwalimu Virtual Tuition Platform Ã‚Â· Effective date: 1 November 2026 Ã‚Â· Version 1.1</p>
                <p><strong>Mwalimu:</strong> Oval Desk Ventures, a business registered in Kenya under registration number BNRRSKQQOZ, of P.O. Box 47210-00100, G.P.O., Nairobi, operating the virtual tuition platform known as Mwalimu ("Mwalimu" or "the Platform").</p>
                <p><strong>The Tutor:</strong> {{ auth()->user()->name }} ("the Tutor").</p>
                <p>This Agreement consists of clauses 1 to 17, Schedule A (Commercial Terms) and Schedule B (Tutor Cancellation, Attendance and No-Show Policy).</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 1. Purpose and scope Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>1. PURPOSE AND SCOPE</h3>
                <p>1.1 Mwalimu operates an online platform that connects learners and their parents or guardians with tutors offering virtual tuition and academic support.</p>
                <p>1.2 The Tutor wishes to provide online tuition to learners through the Platform. Mwalimu agrees to facilitate bookings, virtual learning tools, payment processing and related services, subject to this Agreement.</p>
                <p>1.3 The Tutor shall teach the subjects, grades, topics and curriculum areas approved on the Tutor's profile.</p>
                <p>1.4 This Agreement governs the Tutor's engagement, professional obligations, compensation, conduct, learner safeguarding, confidentiality and use of the Platform.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 2. Appointment Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>2. APPOINTMENT, ELIGIBILITY AND VERIFICATION</h3>
                <p>2.1 Mwalimu appoints the Tutor as an approved service provider on the Platform, subject to successful verification and continued compliance with this Agreement.</p>
                <p>2.2 Before activation, the Tutor shall provide: (a) a valid National ID or passport, and a recent passport-style photograph; (b) academic certificates, transcripts and professional qualifications relevant to the subjects offered; (c) evidence of teaching experience, tutoring experience or subject-matter competence; (d) current contact information and payment details (M-Pesa number or bank account) to which payouts will be made, which must be in the Tutor's own name; (e) references or referee contacts reasonably required by Mwalimu; (f) a valid Certificate of Good Conduct; (g) a valid Teachers Service Commission (TSC) registration or other professional licence, where the Tutor holds or claims one or where it is required for the services offered.</p>
                <p>2.3 Mwalimu may require an interview or teaching demonstration to confirm subject competence.</p>
                <p>2.4 The Tutor warrants that all information and documents submitted are genuine, accurate and complete.</p>
                <p>2.5 The Tutor shall promptly tell Mwalimu of any material change to their qualifications, professional status, contact or payment details, or eligibility to teach.</p>
                <p>2.6 Mwalimu may verify the information provided, request more documentation, conduct reasonable quality assessments, and decline or suspend activation where verification is incomplete or unsatisfactory.</p>
                <p>2.7 The Tutor shall not misrepresent qualifications, claim examiner or marking experience without supporting evidence, or guarantee examination results, grades or admission to any institution.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 3. Nature of relationship Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>3. NATURE OF THE RELATIONSHIP</h3>
                <p>3.1 The Parties intend that the Tutor provides tuition as an independent service provider and not as an employee of Mwalimu, subject to the actual working arrangement.</p>
                <p>3.2 Subject to agreed bookings, safeguarding requirements, Platform rules and applicable law, the Tutor decides their teaching methods, preparation and instructional approach.</p>
                <p>3.3 The Tutor is not entitled to employee benefits from Mwalimu solely because of this Agreement, except where the law provides otherwise.</p>
                <p>3.4 The Tutor is responsible for their own tax, statutory registrations, insurance and business expenses to the extent the law requires.</p>
                <p>3.5 The Tutor shall not represent themselves as an employee, partner, agent or legal representative of Mwalimu, or enter into obligations on its behalf without written authority.</p>
                <p>3.6 Nothing in this Agreement excludes employment rights or statutory protections that apply because of the actual nature of the working relationship.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 4. Tutor profile Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>4. TUTOR PROFILE AND PLATFORM USE</h3>
                <p>4.1 Mwalimu may publish an approved Tutor profile containing the Tutor's name, qualifications, experience, subjects, grades, teaching languages, availability, photograph, biography and other approved information.</p>
                <p>4.2 The Tutor grants Mwalimu a non-exclusive, royalty-free licence, for the term of this Agreement, to use approved profile information, photograph, biography and credentials to operate and promote the Platform.</p>
                <p>4.3 The Tutor shall keep the profile accurate and up to date.</p>
                <p>4.4 The Tutor shall not share account credentials, allow another person to teach using their identity, manipulate reviews, submit fraudulent bookings or otherwise misuse the Platform.</p>
                <p>4.5 Mwalimu may remove, correct or suspend profile content that is inaccurate, misleading, unlawful or inconsistent with its policies.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 5. Bookings and lesson delivery Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>5. BOOKINGS AND LESSON DELIVERY</h3>
                <p>5.1 Lessons are booked through the Platform and delivered in the virtual classroom the Platform provides.</p>
                <p>5.2 Each confirmed booking specifies, where applicable: (a) the learner's grade, subject and topic; (b) the date, start time and duration; (c) the lesson fee; (d) the virtual classroom or meeting method; and (e) any learning objectives or materials.</p>
                <p>5.3 The Tutor shall accept or decline booking requests honestly and within the response period Mwalimu communicates.</p>
                <p>5.4 Having accepted a booking, the Tutor shall attend punctually, prepare suitable materials and deliver the agreed lesson professionally.</p>
                <p>5.5 The Tutor shall: (a) have a suitable device, stable internet connection and a quiet teaching environment; (b) test audio, video, screen-sharing and collaboration tools before the lesson; (c) follow the relevant Kenyan curriculum, or another curriculum expressly agreed with the learner; (d) explain concepts clearly, assess understanding and give constructive feedback; (e) adapt methods reasonably to the learner's grade, needs and agreed objectives; and (f) report significant technical problems or interruptions to Mwalimu promptly.</p>
                <p>5.6 The Tutor shall not knowingly mislead a learner about curriculum coverage, lesson duration, academic progress or expected outcomes.</p>
                <p>5.7 Unless expressly agreed, the Tutor is not required to provide unlimited messaging, homework marking or tuition outside the duration and scope of a paid booking.</p>
                <p>5.8 The Tutor shall not delegate a lesson to another person without Mwalimu's prior written approval and any required verification of the substitute.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 6. Fees, commission and payments Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>6. FEES, COMMISSION AND PAYMENTS</h3>
                <p>6.1 The fee for each lesson is shown with each confirmed booking.</p>
                <p>6.2 The Tutor's compensation is the percentage of the lesson fee set out in Schedule A.</p>
                <p>6.3 Mwalimu collects payment from the learner or parent/guardian, holds it until the session is completed, deducts its commission as set out in Schedule A, and remits the balance to the Tutor. No other deduction is made except a deduction required by law or permitted by this Agreement.</p>
                <p>6.4 Mwalimu pays the Tutor's net amount to the verified M-Pesa or bank account the Tutor has designated, on the payout schedule in Schedule A.</p>
                <p>6.5 Mwalimu may withhold a disputed amount only as far as reasonably necessary to investigate a genuine complaint, suspected fraud, an undelivered lesson, a review request, or a payment reversal. Mwalimu shall tell the Tutor the reason, resolve the matter within a reasonable time, and pay any amount found due in the next payout.</p>
                <p>6.6 If a refund is due because of the Tutor's cancellation, no-show or breach, and the lesson fee has already been paid out, Mwalimu may deduct the amount from future payouts.</p>
                <p>6.7 The Tutor shall not misrepresent lesson completion, collude with a learner to create fraudulent transactions, or claim payment for lessons not delivered.</p>
                <p>6.8 The Tutor shall not collect any additional fee from a parent or learner for a Platform booking.</p>
                <p>6.9 Each Party is responsible for its own tax obligations under Kenyan law.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 7. Cancellations, rescheduling and absence Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>7. CANCELLATIONS, RESCHEDULING AND ABSENCE</h3>
                <p>7.1 The Tutor shall tell Mwalimu and the learner as soon as reasonably possible if unable to attend a confirmed lesson.</p>
                <p>7.2 The Tutor shall comply with Schedule B and the Learner Cancellation, Rescheduling and No-Show Policy, both incorporated into this Agreement.</p>
                <p>7.3 Unless Mwalimu approves an exception: (a) a Tutor cancellation results in a refund, replacement or reschedule for the learner under Schedule B; (b) a Tutor who repeatedly cancels, arrives substantially late or fails to attend may be suspended or removed; (c) a learner cancellation is handled under the Learner Policy, and the Tutor is paid as set out in Schedule B; and (d) a lesson interrupted by a technical failure is assessed fairly, taking account of the cause, the time delivered and whether rescheduling is feasible.</p>
                <p>7.4 No Party shall deliberately mark an unheld lesson as completed.</p>
                <p>7.5 Mwalimu may investigate attendance disputes using booking records, timestamps and other lawfully obtained evidence.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 8. Professional conduct Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>8. PROFESSIONAL CONDUCT AND ACADEMIC INTEGRITY</h3>
                <p>8.1 The Tutor shall act honestly, respectfully, professionally and in the learner's educational interests.</p>
                <p>8.2 The Tutor shall not: (a) discriminate against or harass any learner, parent, guardian or other tutor; (b) behave abusively, threateningly, humiliatingly or in a sexually inappropriate way; (c) encourage cheating, examination malpractice, plagiarism or dishonest completion of assessed work; (d) falsify academic records, qualifications, attendance or progress reports; (e) promote unlawful activities or use lessons for unrelated commercial solicitation; or (f) use the Platform to distribute unlawful, offensive or unauthorised content.</p>
                <p>8.3 The Tutor may guide learners through assignments, explain difficult questions and provide practice exercises, but shall not impersonate a learner or complete work where the learner must demonstrate their own ability.</p>
                <p>8.4 The Tutor shall respect intellectual property rights and use textbooks, past papers, images, software and other resources only where law or licence permits.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 9. Child safeguarding Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>9. CHILD SAFEGUARDING</h3>
                <p style="color: #991B1B; font-weight: 700; background: rgba(239,68,68,.08); padding: 12px; border-radius: 8px; border-left: 3px solid #EF4444;">Ã¢Å¡Â Ã¯Â¸Â This section is critical. Breach of any safeguarding obligation will result in immediate account termination and referral to the relevant authorities.</p>
                <p>9.1 Where a learner is under 18, the Tutor shall respect the learner's right to safety, dignity, privacy and protection from abuse.</p>
                <p>9.2 The Tutor shall comply with the Children Act, 2022, applicable safeguarding requirements and Mwalimu's child protection policy.</p>
                <p>9.3 The Tutor shall: (a) communicate with minors only through the Platform or other channels authorised by Mwalimu and the parent or guardian; (b) keep appropriate professional boundaries at all times; (c) never solicit, exchange or distribute sexually explicit, exploitative or otherwise inappropriate material involving a child; (d) never engage in grooming, sexual harassment, exploitation, threats, intimidation or inappropriate personal relationships with a learner; (e) never ask a minor for unnecessary personal information, private photographs, money, gifts or favours; (f) not arrange an in-person meeting with a minor outside arrangements approved by the parent or guardian and Mwalimu; (g) use any recording or monitoring feature only in line with Mwalimu's disclosed policy, the law and required permissions; and (h) promptly report suspected abuse, exploitation, serious misconduct or an immediate safeguarding concern through Mwalimu's reporting process, and to the authorities where required.</p>
                <p>9.4 The Tutor shall not retaliate against a learner or anyone who raises a safeguarding concern in good faith.</p>
                <p>9.5 Mwalimu may immediately suspend the Tutor's access while investigating a credible safeguarding concern. The investigation will be fair and will respect the safety and privacy of those affected.</p>
                <p>9.6 Where Mwalimu requires background checks, safeguarding training or further documentation, the Tutor shall complete them before teaching the relevant category of learner.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 10. Privacy and data protection Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>10. PRIVACY AND DATA PROTECTION</h3>
                <p>10.1 The Tutor shall comply with the Data Protection Act, 2019, applicable regulations and Mwalimu's privacy and data-handling policies.</p>
                <p>10.2 The Tutor shall use learner information only to deliver authorised tuition, assess learning needs, prepare lessons and carry out other approved educational activities.</p>
                <p>10.3 The Tutor shall keep confidential all personal information about learners, parents, guardians, other tutors and Mwalimu.</p>
                <p>10.4 The Tutor shall not: (a) sell, disclose or commercially exploit learner information; (b) copy or download learner records except where reasonably necessary and authorised; (c) share learner names, contact details, photographs, academic records or lesson content on social media without lawful authority and the required permissions; (d) use learner information for unrelated marketing or private tutoring solicitation; or (e) store learner information, or disclose it to unauthorised persons.</p>
                <p>10.5 The Tutor shall promptly notify Mwalimu of any suspected loss, unauthorised disclosure, account compromise or other personal-data breach involving Platform information.</p>
                <p>10.6 On termination or written request, the Tutor shall return or securely delete personal information no longer lawfully required, subject to legal retention obligations.</p>
                <p>10.7 Nothing in this Agreement authorises the Tutor to record, publish or reuse a learner's image, voice or lesson recording outside approved educational purposes.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 11. Intellectual property Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>11. INTELLECTUAL PROPERTY AND TEACHING MATERIALS</h3>
                <p>11.1 Each Party keeps the intellectual property it owned or developed independently before this Agreement.</p>
                <p>11.2 The Tutor keeps ownership of pre-existing teaching materials, original notes, exercises and other resources developed independently of Mwalimu.</p>
                <p>11.3 The Tutor grants Mwalimu a non-exclusive licence to use materials the Tutor supplies only as far as necessary to facilitate booked lessons and provide the agreed services, unless a broader licence is agreed in writing.</p>
                <p>11.4 The Tutor shall not upload or distribute copyrighted material unless the Tutor owns it or has permission or another lawful basis.</p>
                <p>11.5 The Tutor shall not reproduce or sell Mwalimu's confidential teaching resources, internal training materials, software or proprietary content without written permission.</p>
                <p>11.6 Neither Party shall use the other's trademarks, logo or branding outside the scope of this Agreement without permission.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 12. Lesson recording and quality assurance Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>12. LESSON RECORDING AND QUALITY ASSURANCE</h3>
                <p>12.1 Mwalimu may use learner feedback, attendance information, completion records and quality assessments to monitor service quality and resolve disputes.</p>
                <p>12.2 Lessons may be recorded only where the Platform's recording policy, the law and required notices and permissions allow it.</p>
                <p>12.3 The Tutor shall not independently record, screenshot, livestream or publish lessons involving learners without the necessary authorisation.</p>
                <p>12.4 Mwalimu may ask the Tutor for reasonable cooperation when investigating a complaint, assessing teaching quality or responding to a safeguarding or data-protection concern.</p>
                <p>12.5 The Tutor shall not manipulate reviews or pressure learners or parents to give misleading feedback.</p>
                <p>12.6 Mwalimu shall handle complaints fairly and may give the Tutor an opportunity to respond before disciplinary action, except where immediate protective measures are reasonably necessary.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 13. Conflict of interest Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>13. CONFLICT OF INTEREST AND DIRECT BOOKINGS</h3>
                <p>13.1 The Tutor shall disclose any material conflict of interest that could compromise their impartiality or the learner's interests.</p>
                <p>13.2 The Tutor shall not use confidential learner information obtained through Mwalimu to divert a learner from a confirmed Platform booking or to avoid the Platform's disclosed payment arrangements.</p>
                <p>13.3 Nothing in this Agreement prevents the Tutor from lawful independent teaching or working with other platforms. A booking made through the Platform is always a Platform booking.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 14. Representations, liability and indemnity Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>14. REPRESENTATIONS, LIABILITY AND INDEMNITY</h3>
                <p>14.1 The Tutor warrants that they have the qualifications, competence and legal authority needed to provide the services they advertise.</p>
                <p>14.2 The Tutor shall exercise reasonable professional care and skill in delivering tuition.</p>
                <p>14.3 Each Party is responsible for losses, claims or damage to the extent caused by its own breach of this Agreement, negligence, fraud or unlawful conduct.</p>
                <p>14.4 The Tutor shall indemnify Mwalimu against reasonable, direct losses and third-party claims to the extent they arise from the Tutor's proven fraud, wilful misconduct, material breach of confidentiality, infringement of third-party intellectual property rights or unlawful conduct.</p>
                <p>14.5 Nothing in this Agreement excludes liability where the law prohibits the exclusion.</p>
                <p>14.6 Neither Party is liable for a failure caused by events reasonably beyond its control, provided it promptly notifies the other Party and takes reasonable steps to reduce the impact.</p>
                <p>14.7 Mwalimu does not guarantee the number of bookings, the Tutor's earnings or any academic result.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 15. Suspension and termination Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>15. SUSPENSION AND TERMINATION</h3>
                <p>15.1 Either Party may terminate this Agreement by giving 14 days' written notice.</p>
                <p>15.2 Mwalimu may temporarily suspend the Tutor's profile or access where reasonably necessary to investigate: (a) suspected fraud or falsification of credentials; (b) serious or repeated learner complaints; (c) safeguarding concerns; (d) breaches of privacy or confidentiality; (e) repeated cancellations, non-attendance or poor service; or (f) a legal or regulatory concern affecting the Tutor's eligibility.</p>
                <p>15.3 Mwalimu may terminate this Agreement immediately where there is credible evidence of serious misconduct, abuse, exploitation, fraud, deliberate data misuse, material dishonesty or another serious breach that justifies immediate termination.</p>
                <p>15.4 Where a breach can be remedied and does not need immediate protective action, Mwalimu will ordinarily notify the Tutor and allow a reasonable opportunity to respond or remedy it.</p>
                <p>15.5 On termination: (a) the Tutor stops receiving new bookings; (b) the Tutor shall complete or appropriately hand over existing bookings unless Mwalimu directs otherwise; (c) Mwalimu shall pay all amounts due for delivered lessons in the next payout, less lawful deductions; and (d) the clauses on confidentiality, data protection, intellectual property, accrued payment obligations, liability and dispute resolution survive as far as necessary.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 16. Dispute resolution Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>16. DISPUTE RESOLUTION AND GOVERNING LAW</h3>
                <p>16.1 The Parties shall first try to resolve any dispute through good-faith discussion.</p>
                <p>16.2 If the dispute is unresolved 14 days after written notification, either Party may propose mediation by a mutually agreed mediator in Kenya.</p>
                <p>16.3 If mediation fails or is inappropriate, either Party may refer the dispute to a court or other competent forum in Kenya.</p>
                <p>16.4 This Agreement is governed by the laws of Kenya.</p>
                <p>16.5 Disputes about a specific cancellation, refund or attendance decision are first handled through the review request process in the Learner Policy (48 hours), and then, if unresolved, under this clause.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ 17. General provisions Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>17. GENERAL PROVISIONS</h3>
                <p>17.1 <strong>Entire agreement.</strong> This Agreement, including its Schedules and the policies incorporated under clause 17.7, is the entire agreement between the Parties on its subject matter and replaces earlier discussions on it.</p>
                <p>17.2 <strong>Amendments.</strong> Any material amendment must be communicated in writing and accepted by both Parties, except operational Platform policies, which may be updated in line with their disclosed terms and applicable law.</p>
                <p>17.3 <strong>Severability.</strong> If a provision is held invalid or unenforceable, the rest continues to apply as far as the law allows.</p>
                <p>17.4 <strong>No waiver.</strong> Failure to enforce a provision immediately is not a permanent waiver.</p>
                <p>17.5 <strong>Electronic acceptance.</strong> The Parties may sign electronically or accept this Agreement through an electronic process that reliably records their identity, consent and the version accepted, subject to applicable law.</p>
                <p>17.6 <strong>Incorporated policies.</strong> The following are part of this Agreement and are made available to the Tutor during onboarding: Schedule A, Schedule B, the Learner Cancellation, Rescheduling and No-Show Policy, Mwalimu's child protection policy, and its privacy and data-handling policies. In a conflict about payment, Schedule A prevails. In a conflict about attendance or cancellation, Schedule B prevails over the Learner Policy as far as it concerns the Tutor's obligations and pay.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ Schedule A Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>SCHEDULE A: COMMERCIAL TERMS</h3>

                <p><strong>1. Session fees and how they are shared</strong></p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;color:var(--muted);margin-bottom:16px;">
                    <thead>
                        <tr style="background:rgba(10,100,188,.08);">
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Service</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Session Fee</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Tutor Share</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Tutor Receives</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Mwalimu Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">20-min individual session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 300</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">75%</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 225</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">25% (KSh 75)</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">40-min individual session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 600</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">75%</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 450</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">25% (KSh 150)</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">60-min individual session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 900</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">75%</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 675</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">25% (KSh 225)</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">60-min group session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 600</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">25%</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 150</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">75% (KSh 450)</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">60-min examination clinic</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 1,000</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">25%</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">KSh 250</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">75% (KSh 750)</td></tr>
                    </tbody>
                </table>

                <p><strong>2. Commission basis.</strong> The percentages apply to the full session fee actually paid by the learner. Mwalimu's commission covers payment-processing charges, so none is charged separately to the Tutor. Adjustments for refunds, cancellations and no-shows follow Schedule B.</p>

                <p><strong>3. Payment arrangements</strong></p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;color:var(--muted);margin-bottom:16px;">
                    <tbody>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);font-weight:600;color:var(--ink);width:45%;">Payout frequency</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Once a week, every Friday</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);font-weight:600;color:var(--ink);">Amounts included</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Completed, undisputed sessions up to the weekly payout cut-off communicated by Mwalimu</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);font-weight:600;color:var(--ink);">If Friday is a public holiday</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Next business day</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);font-weight:600;color:var(--ink);">Minimum / Maximum payout threshold</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">None</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);font-weight:600;color:var(--ink);">Payment method</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">M-Pesa or bank transfer, to an account in the Tutor's own name</td></tr>
                    </tbody>
                </table>

                <p><strong>4. Notice period for termination:</strong> 14 days' written notice (clause 15.1).</p>
                <p><strong>5. Commissioned materials:</strong> None unless a separate written agreement is made under clause 11.4.</p>
            </div>

            {{-- Ã¢â€â‚¬Ã¢â€â‚¬ Schedule B Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
            <div class="contract-section">
                <h3>SCHEDULE B: TUTOR CANCELLATION, ATTENDANCE AND NO-SHOW POLICY</h3>
                <p style="color:var(--muted);font-size:13px;">Effective 1 November 2026. Applies to individual tuition, group sessions and examination clinics. Read with the Learner Cancellation, Rescheduling and No-Show Policy.</p>

                <p><strong>1. Commitment to confirmed bookings.</strong> Tutors must offer availability only for sessions they are reasonably certain to deliver. Once a tutor accepts a booking, whether on-demand or scheduled, the tutor must honour it. Tutors must not cancel a confirmed booking merely because another opportunity has come up. A short lead time (e.g. 20 or 40 minutes) does not excuse an accepted booking.</p>

                <p><strong>2. Tutor-initiated cancellations</strong></p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;color:var(--muted);margin-bottom:16px;">
                    <thead>
                        <tr style="background:rgba(10,100,188,.08);">
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Circumstance</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Required action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor cancels immediately after accepting, before the learner is notified or joins</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Notify Mwalimu at once; the cancellation is recorded</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor cancels before the scheduled start</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Notify Mwalimu at once and give a reason</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor cancels after the learner has joined</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Serious service disruption; full learner remedy required</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor fails to attend without notice</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Treated as a tutor no-show</td></tr>
                    </tbody>
                </table>
                <p>Where a tutor cancels a confirmed session: (a) the learner is notified immediately; (b) Mwalimu makes reasonable efforts to find an alternative tutor; (c) the learner may choose an alternative tutor, reschedule, or receive a full refund; (d) the tutor is not paid for a lesson not delivered; and (e) the cancellation is recorded in the tutor's performance history. Repeated or unjustified cancellations may lead to a written warning, temporary suspension, review of the tutor's engagement, or removal from the Platform, after a fair review.</p>

                <p><strong>3. Lateness, no-shows and grace periods.</strong> The same grace period applies to tutors and learners:</p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;color:var(--muted);margin-bottom:16px;">
                    <thead>
                        <tr style="background:rgba(10,100,188,.08);">
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Session</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Grace period</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">20-minute session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">3 minutes</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">40-minute session</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">5 minutes</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">60-minute session, group session or examination clinic</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">10 minutes</td></tr>
                    </tbody>
                </table>
                <p>If the tutor has not joined when the grace period ends and has not communicated, the learner may report the tutor absent, and Mwalimu will investigate and decide the remedy. A tutor who joins late should make reasonable efforts to deliver the full paid duration, subject to the learner's agreement and the schedule. If the full lesson cannot be delivered, the learner must be offered a replacement, credit or refund. A tutor no-show results in no payment for the undelivered lesson, a full refund or replacement session for the learner, a recorded performance incident, and further disciplinary action where warranted.</p>

                <p><strong>4. Learner no-shows.</strong> If the learner has not joined when the grace period ends and has not communicated, the tutor should remain in the classroom until the grace period has ended, and may then report the learner absent through the Platform. Reports must be accurate; false reports are a breach of this Agreement.</p>

                <p><strong>5. How the tutor is paid when a learner cancels or is absent</strong></p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;color:var(--muted);margin-bottom:16px;">
                    <thead>
                        <tr style="background:rgba(10,100,188,.08);">
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Situation</th>
                            <th style="padding:8px 10px;text-align:left;border:1px solid rgba(21,40,31,.15);color:var(--ink);">Tutor payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Lesson delivered in full</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Full tutor share</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Learner no-show (grace period ended, no contact)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Full tutor share</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">On-demand booking cancelled within 10 minutes before tutor joins (full refund)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">None</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">On-demand booking cancelled after the tutor has joined (no refund)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Full tutor share</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Scheduled booking cancelled at least 1 hour before (full refund or free reschedule)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">None for the cancelled booking; paid for the rescheduled session when delivered</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Scheduled booking cancelled 30 to 60 minutes before (50% refund)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor share calculated on the 50% retained</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Scheduled booking cancelled less than 30 minutes before (no automatic refund)</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Full tutor share</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Group session or clinic cancelled by a learner</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor share unchanged where the session goes ahead; learner refunds do not reduce the tutor share unless the session is cancelled</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor cancellation or tutor no-show</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">None</td></tr>
                        <tr style="background:rgba(10,100,188,.02);"><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Lesson partly delivered because of a tutor-side technical failure</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Tutor share pro-rated to the time delivered</td></tr>
                        <tr><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">Verified Mwalimu platform failure</td><td style="padding:8px 10px;border:1px solid rgba(21,40,31,.1);">None for the undelivered session, with no penalty to the tutor</td></tr>
                    </tbody>
                </table>

                <p><strong>6. Technical difficulties.</strong> A tutor with a technical problem must notify Mwalimu at once and make reasonable efforts to restore the connection. If a lesson cannot proceed or is substantially disrupted, the learner is offered a replacement session or appropriate refund. A verified platform failure does not count against the tutor.</p>

                <p><strong>7. Emergencies.</strong> Mwalimu may consider exceptions for medical emergencies, accidents, bereavement, serious family emergencies, major internet or electricity outages and other circumstances reasonably beyond the person's control. The affected tutor must notify Mwalimu as soon as possible.</p>

                <p><strong>8. Review requests.</strong> A tutor who disagrees with a cancellation or attendance decision may submit a review request within 48 hours of the incident with the booking reference, lesson date and time, a description and any evidence. Mwalimu reviews booking records, communications, payment records and platform logs, and any payout for the disputed session is held until the decision.</p>

                <p><strong>9. Acknowledgement.</strong> By accepting bookings through Mwalimu, the tutor accepts the cancellation, attendance and performance requirements in this Schedule.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('accept-tutor-contract') }}" id="contractForm">
            @csrf
            <div class="accept-box">
                <label class="checkbox-label">
                    <input type="checkbox" name="accept_contract" id="acceptCheckbox" required>
                    <span style="line-height: 1.6; color: var(--ink);">
                        <strong>Tutor's Declaration:</strong><br>
                        1. I confirm that I have read this whole Agreement, including Schedules A and B and the Learner Cancellation, Rescheduling and No-Show Policy, and had the opportunity to save or download a copy.<br>
                        2. I agree to be legally bound by this Agreement and all its terms, including the professional conduct, safeguarding, privacy, academic integrity, payment, and cancellation obligations.<br>
                        3. All qualifications, credentials, and information I have provided are genuine, accurate, and complete.<br>
                        4. I will promptly notify Mwalimu of any material change to my qualifications, professional status, contact or payment details, or eligibility to teach.<br>
                        5. I understand this is a legally binding agreement governed by the laws of Kenya.
                    </span>
                </label>

                <button type="submit" class="btn-accept" id="acceptButton" disabled>
                    <i class="fa-solid fa-circle-check"></i> Agree &amp; Sign Ã¢â‚¬â€ I Accept the Agreement
                </button>

                <p style="text-align: center; font-size: 12px; color: var(--muted); margin-top: 12px;">
                    Clicking above is my electronic acceptance and signature as of {{ now()->format('F d, Y') }}.
                    This constitutes a legally binding agreement under Kenyan law.
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
