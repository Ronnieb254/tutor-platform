<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Contact Us – Mwalimu</title>
<meta name="description" content="Get in touch with Mwalimu by OvalDesk Ventures. Suite 11, Samima Suites, Nairobi West.">
</head>
<body>
@include('partials.nav')

{{-- HERO --}}
<section style="background:linear-gradient(135deg,#111C16,#15281F);padding:64px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;right:-80px;width:300px;height:300px;background:rgba(109,40,217,.12);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;left:10%;width:180px;height:180px;background:rgba(212,160,23,.06);border-radius:50%;pointer-events:none;"></div>
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(109,40,217,.3);color:rgba(255,255,255,.9);padding:6px 18px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:20px;">
            <i class="fa-solid fa-envelope fa-xs"></i> Get in Touch
        </div>
        <h1 style="font-family:'Fraunces',serif;font-size:clamp(32px,5vw,52px);color:#fff;margin-bottom:14px;">We'd love to hear from you</h1>
        <p style="font-size:17px;color:rgba(255,255,255,.6);max-width:480px;margin:0 auto;">Whether you're a learner, parent, or tutor — our team is here to help.</p>
    </div>
</section>

{{-- MAIN --}}
<section style="padding:64px 0;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;">
        <div style="display:grid;grid-template-columns:1fr;gap:28px;" class="contact-g">

            {{-- LEFT: Info + Quick links --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Contact card --}}
                <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px;">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:22px;padding-bottom:20px;border-bottom:1px solid rgba(21,40,31,.08);">
                        <div style="width:52px;height:52px;border-radius:16px;background:#15281F;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="28" height="28" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                        </div>
                        <div>
                            <h3 style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#15281F;">Mwalimu</h3>
                            <p style="font-size:13px;color:rgba(21,40,31,.5);">by OvalDesk Ventures</p>
                        </div>
                    </div>

                    @foreach([
                        ['fa-location-dot','Office','Suite 11, Samima Suites, Nairobi West',null],
                        ['fa-envelope','Email','info@ovaldesk.co.ke','mailto:info@ovaldesk.co.ke'],
                        ['fa-phone','Phone','0724 367 814 / 0723 636 901','tel:+254724367814'],
                        ['fa-globe','Website','ovaldesk.co.ke','https://ovaldesk.co.ke'],
                    ] as [$ic,$label,$val,$href])
                    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;">
                        <div style="width:40px;height:40px;background:rgba(109,40,217,.08);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:#6D28D9;font-size:14px;"></i>
                        </div>
                        <div>
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.4);margin-bottom:2px;">{{ $label }}</p>
                            @if($href)
                            <a href="{{ $href }}" {{ str_starts_with($href,'http')?'target="_blank"':'' }} style="font-size:14px;color:#6D28D9;font-weight:500;text-decoration:none;transition:opacity .15s;" onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">{{ $val }}</a>
                            @else
                            <p style="font-size:14px;color:#15281F;font-weight:500;">{{ $val }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Quick links --}}
                <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:24px;">
                    <h3 style="font-family:'Fraunces',serif;font-size:17px;color:#15281F;margin-bottom:16px;display:flex;align-items:center;gap:7px;">
                        <i class="fa-solid fa-link" style="color:#6D28D9;font-size:14px;"></i> Quick Links
                    </h3>
                    @foreach([
                        ['fa-magnifying-glass','Find a Tutor',route('tutors.index'),'Browse verified tutors'],
                        ['fa-star','Examiner\'s Clinic',route('examiner-clinic'),'Group sessions with national examiners'],
                        ['fa-circle-info','How It Works',route('how-it-works'),'Step-by-step guide'],
                        ['fa-chalkboard-user','Become a Tutor',route('register').'?role=tutor','Join as a verified tutor'],
                    ] as [$ic,$label,$url,$desc])
                    <a href="{{ $url }}" style="display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid rgba(21,40,31,.06);text-decoration:none;transition:opacity .15s;" onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                        <div style="width:34px;height:34px;background:rgba(109,40,217,.08);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $ic }}" style="color:#6D28D9;font-size:12px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px;font-weight:600;color:#15281F;">{{ $label }}</div>
                            <div style="font-size:12px;color:rgba(21,40,31,.5);">{{ $desc }}</div>
                        </div>
                        <i class="fa-solid fa-arrow-right fa-xs" style="color:rgba(21,40,31,.3);"></i>
                    </a>
                    @endforeach
                </div>

                {{-- FAQ --}}
                <div style="background:rgba(109,40,217,.05);border:1px solid rgba(109,40,217,.15);border-radius:18px;padding:22px;">
                    <h3 style="font-size:15px;font-weight:700;color:#15281F;margin-bottom:12px;display:flex;align-items:center;gap:7px;">
                        <i class="fa-solid fa-circle-question" style="color:#6D28D9;"></i> Common Questions
                    </h3>
                    @foreach(['How are tutors verified?','What if I\'m not satisfied with my session?','How do I join a live session?','How do tutors get paid?'] as $q)
                    <a href="{{ route('how-it-works') }}" style="display:flex;align-items:center;gap:8px;padding:8px 0;font-size:13px;font-weight:500;color:#6D28D9;text-decoration:none;border-bottom:1px solid rgba(109,40,217,.1);">
                        <i class="fa-solid fa-chevron-right fa-xs"></i>{{ $q }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Contact form --}}
            <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:36px;box-shadow:0 4px 24px rgba(21,40,31,.06);">
                <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:6px;">Send us a message</h2>
                <p style="font-size:14px;color:rgba(21,40,31,.55);margin-bottom:28px;">We typically respond within 24 hours on business days.</p>

                @if(session('contact_success'))
                <div style="background:#DCFCE7;border:1px solid #BBF7D0;border-radius:14px;padding:16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#166534;font-size:14px;font-weight:500;">
                    <i class="fa-solid fa-circle-check"></i> Message sent! We'll get back to you within 24 hours.
                </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;" class="form-row">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                                <i class="fa-solid fa-user fa-xs" style="color:#6D28D9;"></i> Full Name
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Mwangi" required
                                   style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:all .15s;"
                                   onfocus="this.style.borderColor='#6D28D9';this.style.boxShadow='0 0 0 3px rgba(109,40,217,.1)'" onblur="this.style.borderColor='rgba(21,40,31,.18)';this.style.boxShadow='none'">
                            @error('name')<p style="font-size:12px;color:#E8522A;margin-top:3px;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                                <i class="fa-solid fa-phone fa-xs" style="color:#6D28D9;"></i> Phone
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="0712 345 678"
                                   style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:all .15s;"
                                   onfocus="this.style.borderColor='#6D28D9';this.style.boxShadow='0 0 0 3px rgba(109,40,217,.1)'" onblur="this.style.borderColor='rgba(21,40,31,.18)';this.style.boxShadow='none'">
                        </div>
                    </div>
                    <style>.form-row{grid-template-columns:1fr!important;}@media(min-width:480px){.form-row{grid-template-columns:1fr 1fr!important;}}</style>

                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                            <i class="fa-solid fa-envelope fa-xs" style="color:#6D28D9;"></i> Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required
                               style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:all .15s;"
                               onfocus="this.style.borderColor='#6D28D9';this.style.boxShadow='0 0 0 3px rgba(109,40,217,.1)'" onblur="this.style.borderColor='rgba(21,40,31,.18)';this.style.boxShadow='none'">
                        @error('email')<p style="font-size:12px;color:#E8522A;margin-top:3px;">{{ $message }}</p>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;" class="form-row">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                                <i class="fa-solid fa-tag fa-xs" style="color:#6D28D9;"></i> I am a…
                            </label>
                            <select name="role" style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;appearance:none;background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2210%22 height=%226%22><path d=%22M1 1l4 4 4-4%22 stroke=%22%2315281F%22 stroke-width=%221.5%22 fill=%22none%22/></svg>');background-repeat:no-repeat;background-position:right 14px center;transition:all .15s;" onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                                <option value="">Select role</option>
                                <option value="student" {{ old('role')==='student'?'selected':'' }}>Student</option>
                                <option value="parent" {{ old('role')==='parent'?'selected':'' }}>Parent</option>
                                <option value="tutor" {{ old('role')==='tutor'?'selected':'' }}>Tutor / Examiner</option>
                                <option value="other" {{ old('role')==='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                                <i class="fa-solid fa-book-open fa-xs" style="color:#6D28D9;"></i> Subject / Topic
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="e.g. Mathematics – Form 3"
                                   style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:all .15s;"
                                   onfocus="this.style.borderColor='#6D28D9'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                        </div>
                    </div>

                    <div style="margin-bottom:22px;">
                        <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;">
                            <i class="fa-solid fa-message fa-xs" style="color:#6D28D9;"></i> Message
                        </label>
                        <textarea name="message" rows="4" placeholder="Tell us how we can help…" required
                                  style="width:100%;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:vertical;line-height:1.65;background:#fff;transition:all .15s;"
                                  onfocus="this.style.borderColor='#6D28D9';this.style.boxShadow='0 0 0 3px rgba(109,40,217,.1)'" onblur="this.style.borderColor='rgba(21,40,31,.18)';this.style.boxShadow='none'">{{ old('message') }}</textarea>
                        @error('message')<p style="font-size:12px;color:#E8522A;margin-top:3px;">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                            style="width:100%;height:52px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
                            onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-paper-plane fa-sm"></i> Send Message
                    </button>
                </form>

                <div style="margin-top:22px;padding-top:18px;border-top:1px solid rgba(21,40,31,.08);text-align:center;">
                    <p style="font-size:13px;color:rgba(21,40,31,.5);margin-bottom:10px;">Or reach us directly</p>
                    <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                        <a href="mailto:info@ovaldesk.co.ke" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:999px;border:1.5px solid rgba(21,40,31,.15);color:#15281F;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='#6D28D9';this.style.color='#6D28D9'" onmouseout="this.style.borderColor='rgba(21,40,31,.15)';this.style.color='#15281F'">
                            <i class="fa-solid fa-envelope fa-xs"></i> info@ovaldesk.co.ke
                        </a>
                        <a href="tel:+254724367814" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:999px;border:1.5px solid rgba(21,40,31,.15);color:#15281F;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='#6D28D9';this.style.color='#6D28D9'" onmouseout="this.style.borderColor='rgba(21,40,31,.15)';this.style.color='#15281F'">
                            <i class="fa-solid fa-phone fa-xs"></i> 0724 367 814
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>.contact-g{grid-template-columns:1fr!important;}@media(min-width:1024px){.contact-g{grid-template-columns:380px 1fr!important;}}</style>

@include('partials.footer')
@livewireScripts
</body>
</html>
