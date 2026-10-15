<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')

    
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact Us – Mwalimu</title>
<meta name="description" content="Get in touch with Mwalimu by OvalDesk Ventures. Suite 11, Samima Suites, Nairobi West. Email: info@ovaldesk.co.ke">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@livewireStyles
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--violet:#6D28D9;--coral:#E8522A;--gold:#D4A017;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;}
h1,h2,h3{font-family:'Fraunces',Georgia,serif;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--violet);color:#fff;}.btn-v:hover{background:#4C1D95;transform:translateY(-1px);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);}.btn-o:hover{background:var(--ink);color:var(--cream);}
.card{background:#fff;border-radius:22px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
nav{position:sticky;top:0;z-index:100;background:rgba(251,245,230,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px;}
.logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:var(--ink);}
.logo em{font-style:normal;color:var(--violet);}
.field{margin-bottom:16px;}
.field label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:6px;margin-bottom:6px;}
.field label i{color:var(--violet);}
.field input,.field select,.field textarea{width:100%;padding:12px 16px;border-radius:14px;border:1.5px solid rgba(21,40,31,.18);background:#fff;font-size:14px;font-family:inherit;color:var(--ink);outline:none;transition:all .15s;}
.field input,.field select{height:48px;}
.field textarea{resize:vertical;min-height:120px;line-height:1.65;}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--violet);box-shadow:0 0 0 3px rgba(109,40,217,.1);}
</style>
</head>
<body>

{{-- NAV --}}
@include('partials.nav')

{{-- HERO --}}
<section style="background:var(--ink);padding:60px 0;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-60px;width:250px;height:250px;background:rgba(109,40,217,.15);border-radius:50%;"></div>
    <div class="w" style="position:relative;z-index:1;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(109,40,217,.3);color:rgba(255,255,255,.9);padding:6px 16px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:18px;">
            <i class="fa-solid fa-envelope"></i> Get in Touch
        </div>
        <h1 style="font-size:clamp(32px,5vw,52px);color:#fff;margin-bottom:14px;">We'd love to hear from you</h1>
        <p style="font-size:17px;color:rgba(255,255,255,.6);max-width:500px;margin:0 auto;">Whether you're a learner, parent, or tutor — our team is ready to help. Reach out through any of the channels below.</p>
    </div>
</section>

{{-- MAIN CONTACT SECTION --}}
<section style="padding:72px 0;">
    <div class="w">
        <div style="display:grid;grid-template-columns:1fr;gap:32px;max-width:1100px;margin:0 auto;" class="contact-g">

            {{-- CONTACT INFO --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- OvalDesk info card --}}
                <div class="card" style="padding:28px;">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--hair);">
                        <div style="width:52px;height:52px;background:var(--ink);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                        </div>
                        <div>
                            <h3 style="font-family:'Fraunces',serif;font-size:20px;color:var(--ink);">Mwalimu</h3>
                            <p style="font-size:13px;color:var(--muted);">by OvalDesk Ventures</p>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        @foreach([
                            ['fa-location-dot','Address','Suite 11, Samima Suites, Nairobi West'],
                            ['fa-envelope','Email','info@ovaldesk.co.ke'],
                            ['fa-phone','Phone','0724 367 814 / 0723 636 901'],
                            ['fa-globe','Website','https://ovaldesk.co.ke'],
                        ] as [$icon,$label,$value])
                        <div style="display:flex;align-items:flex-start;gap:14px;">
                            <div style="width:40px;height:40px;background:rgba(109,40,217,.08);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid {{ $icon }}" style="color:var(--violet);font-size:15px;"></i>
                            </div>
                            <div>
                                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:2px;">{{ $label }}</p>
                                @if($label === 'Website')
                                <a href="{{ $value }}" target="_blank" style="font-size:14px;color:var(--violet);font-weight:500;">{{ $value }}</a>
                                @elseif($label === 'Email')
                                <a href="mailto:{{ $value }}" style="font-size:14px;color:var(--violet);font-weight:500;">{{ $value }}</a>
                                @elseif($label === 'Phone')
                                <a href="tel:+254724367814" style="font-size:14px;color:var(--ink);font-weight:500;">{{ $value }}</a>
                                @else
                                <p style="font-size:14px;color:var(--ink);font-weight:500;">{{ $value }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Quick links --}}
                <div class="card" style="padding:24px;">
                    <h3 style="font-size:16px;color:var(--ink);margin-bottom:16px;"><i class="fa-solid fa-link fa-sm" style="color:var(--violet);margin-right:6px;"></i> Quick Links</h3>
                    @foreach([
                        ['fa-magnifying-glass','Find a Tutor', route('tutors.index'),'Browse verified tutors'],
                        ['fa-star','Examiner\'s Clinic','/examiner-clinic','Group sessions with national examiners'],
                        ['fa-circle-info','How It Works', route('how-it-works'),'Step-by-step guide'],
                        ['fa-chalkboard-user','Become a Tutor', route('register').'?role=tutor','Join as a verified tutor'],
                    ] as [$icon,$label,$url,$desc])
                    <a href="{{ $url }}" style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid rgba(21,40,31,.05);transition:color .15s;" onmouseover="this.querySelector('span').style.color='var(--violet)'" onmouseout="this.querySelector('span').style.color='var(--ink)'">
                        <div style="width:34px;height:34px;background:rgba(109,40,217,.08);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $icon }}" style="color:var(--violet);font-size:13px;"></i>
                        </div>
                        <div>
                            <span style="font-size:14px;font-weight:600;color:var(--ink);display:block;transition:color .15s;">{{ $label }}</span>
                            <span style="font-size:12px;color:var(--muted);">{{ $desc }}</span>
                        </div>
                        <i class="fa-solid fa-arrow-right fa-xs" style="color:var(--muted);margin-left:auto;"></i>
                    </a>
                    @endforeach
                </div>

                {{-- FAQ quick links --}}
                <div style="background:rgba(109,40,217,.06);border:1px solid rgba(109,40,217,.15);border-radius:18px;padding:22px;">
                    <h3 style="font-size:15px;color:var(--ink);margin-bottom:10px;"><i class="fa-solid fa-circle-question" style="color:var(--violet);margin-right:6px;"></i> Common Questions</h3>
                    @foreach(['How are tutors verified?','What happens if I\'m not satisfied?','How do I join a session?','How do tutors get paid?'] as $q)
                    <a href="{{ route('how-it-works') }}" style="display:flex;align-items:center;gap:8px;padding:8px 0;font-size:13px;color:var(--violet);border-bottom:1px solid rgba(109,40,217,.1);">
                        <i class="fa-solid fa-chevron-right fa-xs"></i>{{ $q }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- CONTACT FORM --}}
            <div class="card" style="padding:36px;">
                <h2 style="font-size:26px;color:var(--ink);margin-bottom:6px;">Send us a message</h2>
                <p style="font-size:14px;color:var(--muted);margin-bottom:28px;">We typically respond within 24 hours on business days.</p>

                @if(session('contact_success'))
                <div style="background:#DCFCE7;border:1px solid #BBF7D0;border-radius:14px;padding:16px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#166534;font-weight:500;">
                    <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
                    Message sent! We'll get back to you within 24 hours.
                </div>
                @endif

                <form method="POST" action="/contact">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;" class="row2">
                        <div class="field">
                            <label><i class="fa-solid fa-user"></i> Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Mwangi" required>
                            @error('name')<p style="font-size:12px;color:var(--coral);margin-top:3px;">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label><i class="fa-solid fa-phone"></i> Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="0712 345 678">
                        </div>
                    </div>
                    <style>.row2{grid-template-columns:1fr!important;}@media(min-width:640px){.row2{grid-template-columns:1fr 1fr!important;}}</style>

                    <div class="field">
                        <label><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                        @error('email')<p style="font-size:12px;color:var(--coral);margin-top:3px;">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label><i class="fa-solid fa-tag"></i> I am a…</label>
                        <select name="role">
                            <option value="">Select your role</option>
                            <option value="student" {{ old('role')==='student'?'selected':'' }}>Student</option>
                            <option value="parent" {{ old('role')==='parent'?'selected':'' }}>Parent</option>
                            <option value="tutor" {{ old('role')==='tutor'?'selected':'' }}>Tutor / Examiner</option>
                            <option value="other" {{ old('role')==='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>

                    <div class="field">
                        <label><i class="fa-solid fa-list"></i> Subject / Topic</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="e.g. Mathematics – Form 3, Algebra">
                    </div>

                    <div class="field">
                        <label><i class="fa-solid fa-message"></i> Message</label>
                        <textarea name="message" placeholder="Tell us how we can help…" required>{{ old('message') }}</textarea>
                        @error('message')<p style="font-size:12px;color:var(--coral);margin-top:3px;">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn btn-v" style="width:100%;justify-content:center;padding:14px;font-size:15px;margin-top:4px;">
                        <i class="fa-solid fa-paper-plane"></i> Send Message
                    </button>
                </form>

                <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--hair);text-align:center;">
                    <p style="font-size:13px;color:var(--muted);margin-bottom:10px;">Or reach us directly</p>
                    <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
                        <a href="mailto:info@ovaldesk.co.ke" class="btn btn-o" style="font-size:13px;padding:9px 20px;"><i class="fa-solid fa-envelope"></i> info@ovaldesk.co.ke</a>
                        <a href="tel:+254724367814" class="btn btn-o" style="font-size:13px;padding:9px 20px;"><i class="fa-solid fa-phone"></i> 0724 367 814</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>.contact-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.contact-g{grid-template-columns:420px 1fr!important;}}</style>
</section>

{{-- FOOTER --}}
<!-- <footer style="background:var(--ink);padding:36px 0;">
    <div class="w" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:8px;font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:#fff;">
            <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            Mwalimu
        </a>
        <span style="font-size:12px;color:rgba(255,255,255,.3);">&copy; {{ date('Y') }} Mwalimu by OvalDesk Ventures</span>
    </div>
</footer> -->
<style>.blog-show-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.blog-show-g{grid-template-columns:1fr 300px!important;}}</style>
@include('partials.footer')

@livewireScripts
</body>
</html>
