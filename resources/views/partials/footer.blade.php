<footer class="mw-footer">
    <div class="mw-footer-main">
        <div class="w">
            <div class="mw-footer-grid">
                <div class="mw-footer-brand">
                    <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#fff;">
                        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                        <span style="color:#fff;"><em style="font-style:normal;color:#a78bfa;">Mw</em>alimu</span>
                    </a>
                    <p>Instant 1-on-1 tutoring, matched in seconds. TSC-registered teachers. Secure classrooms. 100% money-back on your first lesson.</p>
                    <div style="margin-top:16px;display:flex;flex-direction:column;gap:6px;">
                        <div style="font-size:13px;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:7px;">
                            <i class="fa-solid fa-envelope fa-xs" style="color:rgba(255,255,255,.3);width:14px;text-align:center;"></i>
                            <a href="mailto:info@ovaldesk.co.ke" style="color:rgba(255,255,255,.6);transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">info@ovaldesk.co.ke</a>
                        </div>
                        <div style="font-size:13px;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:7px;">
                            <i class="fa-solid fa-phone fa-xs" style="color:rgba(255,255,255,.3);width:14px;text-align:center;"></i>
                            <a href="tel:+254724367814" style="color:rgba(255,255,255,.6);transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">0724 367 814 / 0723 636 901</a>
                        </div>
                        <div style="font-size:13px;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:7px;">
                            <i class="fa-solid fa-location-dot fa-xs" style="color:rgba(255,255,255,.3);width:14px;text-align:center;"></i>
                            <span>Suite 11, Samima Suites, Nairobi West</span>
                        </div>
                    </div>
                </div>
                <div class="mw-footer-col">
                    <h4>For Learners</h4>
                    <a href="{{ route('tutors.index') }}"><i class="fa-solid fa-magnifying-glass fa-xs" style="margin-right:6px;opacity:.4;"></i>Find a Tutor</a>
                    <a href="{{ route('examiner-clinic') }}"><i class="fa-solid fa-star fa-xs" style="margin-right:6px;opacity:.4;"></i>Examiner's Clinic</a>
                    <a href="{{ route('how-it-works') }}"><i class="fa-solid fa-circle-info fa-xs" style="margin-right:6px;opacity:.4;"></i>How It Works</a>
                    <a href="{{ route('courses.index') }}"><i class="fa-solid fa-play-circle fa-xs" style="margin-right:6px;opacity:.4;"></i>Courses</a>
                    <a href="{{ route('blog.index') }}"><i class="fa-solid fa-newspaper fa-xs" style="margin-right:6px;opacity:.4;"></i>Blog</a>
                </div>
                <div class="mw-footer-col">
                    <h4>For Tutors</h4>
                    <a href="{{ route('register') }}?role=tutor"><i class="fa-solid fa-chalkboard-user fa-xs" style="margin-right:6px;opacity:.4;"></i>Become a Tutor</a>
                    <a href="{{ route('how-it-works') }}#tutor"><i class="fa-solid fa-id-card-clip fa-xs" style="margin-right:6px;opacity:.4;"></i>Verification Process</a>
                    <a href="{{ route('examiner-clinic') }}#examiners"><i class="fa-solid fa-star fa-xs" style="margin-right:6px;opacity:.4;"></i>Host a Clinic</a>
                    <a href="#"><i class="fa-solid fa-share-nodes fa-xs" style="margin-right:6px;opacity:.4;"></i>Referral Programme</a>
                    <a href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket fa-xs" style="margin-right:6px;opacity:.4;"></i>Tutor Login</a>
                </div>
                <div class="mw-footer-col">
                    <h4>Company</h4>
                    <a href="{{ route('contact') }}"><i class="fa-solid fa-envelope fa-xs" style="margin-right:6px;opacity:.4;"></i>Contact Us</a>
                    <a href="https://ovaldesk.co.ke" target="_blank"><i class="fa-solid fa-globe fa-xs" style="margin-right:6px;opacity:.4;"></i>OvalDesk Ventures</a>
                    <a href="#"><i class="fa-solid fa-shield-halved fa-xs" style="margin-right:6px;opacity:.4;"></i>Privacy Policy</a>
                    <a href="#"><i class="fa-solid fa-file-contract fa-xs" style="margin-right:6px;opacity:.4;"></i>Terms of Service</a>
                    <a href="#"><i class="fa-solid fa-cookie-bite fa-xs" style="margin-right:6px;opacity:.4;"></i>Cookie Policy</a>
                </div>
            </div>
            <div class="mw-footer-bottom">
                <span>&copy; {{ date('Y') }} Mwalimu by <a href="https://ovaldesk.co.ke" style="color:rgba(255,255,255,.4);transition:color .15s;" target="_blank" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.4)'">OvalDesk Ventures</a>. All rights reserved.</span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-certificate fa-xs" style="color:var(--mint);"></i>
                    <span>TSC-Registered Tutors</span>
                    <span style="margin:0 4px;">·</span>
                    <i class="fa-solid fa-shield-halved fa-xs" style="color:var(--mint);"></i>
                    <span>Secure Sessions</span>
                    <span style="margin:0 4px;">·</span>
                    <i class="fa-solid fa-rotate-left fa-xs" style="color:var(--mint);"></i>
                    <span>100% Money-Back</span>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Cookie Banner --}}
<div x-data="{ show: !localStorage.getItem('mwalimu_cookie') }" x-show="show" x-cloak class="cookie-banner">
    <div class="cookie-inner">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <i class="fa-solid fa-cookie-bite" style="font-size:22px;color:var(--gold);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <p style="font-weight:700;font-size:14px;color:var(--ink);margin-bottom:3px;">We value your privacy</p>
                <p style="font-size:12px;color:var(--muted);line-height:1.5;">We use cookies to personalise your experience and keep your session secure.</p>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button @click="localStorage.setItem('mwalimu_cookie','min');show=false" style="padding:8px 18px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;color:var(--ink);border:1.5px solid var(--ink);cursor:pointer;font-family:inherit;transition:all .15s;">Necessary Only</button>
            <button @click="localStorage.setItem('mwalimu_cookie','all');show=false" class="btn btn-v btn-sm"><i class="fa-solid fa-check"></i> Accept All</button>
        </div>
    </div>
</div>
