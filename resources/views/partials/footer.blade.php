<footer style="background:#0D1F14;color:rgba(255,255,255,.65);">
 <div style="background:linear-gradient(135deg,#15281F,#111C16);padding:64px 0 0;">
 <div style="max-width:1360px;margin:0 auto;padding:0 24px;">

 {{-- Top grid --}}
 <div style="display:grid;grid-template-columns:1fr;gap:40px;margin-bottom:48px;" class="mw-foot-grid">

 {{-- Brand column --}}
 <div>
 <a href="{{ route('home') }}" style="display:inline-block;margin-bottom:16px;">
 <img src="/images/logo.svg" alt="Mwalimu" style="height:38px;width:auto;">
 </a>
 <p style="font-size:14px;color:rgba(255,255,255,.45);line-height:1.8;max-width:280px;margin-bottom:20px;">Instant 1-on-1 tutoring, matched in seconds. TSC-registered teachers. Secure classrooms. 100% money-back if unsatisfied with your first lesson.</p>

 {{-- Trust badges --}}
 <!-- <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
 @foreach([
 ['fa-certificate','TSC-Registered Tutors','#10B981'],
 ['fa-shield-halved','Background Checked','#10B981'],
 ['fa-rotate-left','100% Money-Back Guarantee','#10B981'],
 ] as [$ic,$label,$color])
 <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
 <i class="fa-solid {{ $ic }}"></i>
 {{ $label }}
 </div>
 @endforeach
 </div> -->

 {{-- Contact details --}}
 <div style="display:flex;flex-direction:column;gap:6px;">
 @foreach([
 ['fa-envelope','support@ovaldesk.co.ke','mailto:support@ovaldesk.co.ke'],
 ['fa-phone','0724 367 814 / 0723 636 901','tel:+254724367814'],
 ['fa-location-dot','Suite 11, Samima Suites, Nairobi West',null],
 ] as [$ic,$text,$href])
 <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(255,255,255,.5);">
 <i class="fa-solid {{ $ic }}"></i>
 @if($href)
 <a href="{{ $href }}" style="color:rgba(255,255,255,.6);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">{{ $text }}</a>
 @else
 <span>{{ $text }}</span>
 @endif
 </div>
 @endforeach
 </div>
 </div>

 {{-- For Learners --}}
 <div>
 <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">For Learners</h4>
 @foreach([
 ['fa-magnifying-glass','Find a Tutor', route('tutors.index')],
 ['fa-users', 'Group Sessions', route('group-sessions.index')],
 ['fa-star', "Examiner's Clinic", route('examiner-clinic')],
 ['fa-book-open', 'Library', route('library.index')],
 ['fa-circle-info', 'How It Works', route('how-it-works')],
 ['fa-envelope', 'Contact Us', route('contact')],
 ] as [$ic,$label,$url])
 <a href="{{ $url }}" style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
 <i class="fa-solid {{ $ic }}"></i>
 {{ $label }}
 </a>
 @endforeach
 @auth
 <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
 <i class="fa-solid fa-chart-bar"></i>
 Dashboard
 </a>
 @endauth
 </div>

 {{-- For Tutors --}}
 <div>
 <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">For Tutors</h4>
 @foreach([
 ['fa-chalkboard-user', 'Become a Tutor', route('register').'?role=tutor'],
 ['fa-id-card-clip', 'Verification Process', route('how-it-works')],
 ['fa-right-to-bracket','Tutor Login', route('login')],
 ['fa-gauge-high', 'Dashboard', auth()->check() ? route('dashboard') : route('login')],
 ] as [$ic,$label,$url])
 @if($label === 'Become a Tutor' && auth()->check() && auth()->user() && in_array(auth()->user()->role, ['tutor','admin']))
 @continue
 @endif
 @if($label === 'Tutor Login' && auth()->check())
 @continue
 @endif
 <a href="{{ $url }}" style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
 <i class="fa-solid {{ $ic }}"></i>
 {{ $label }}
 </a>
 @endforeach
 </div>

 {{-- Company --}}
 <div>
 <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:16px;">Company</h4>
 @foreach([
 ['fa-building', 'OvalDesk Ventures', 'https://ovaldesk.co.ke'],
 ['fa-envelope', 'Contact Us', route('contact')],
 ['fa-shield-halved', 'Privacy Policy', route('privacy-policy')],
 ['fa-file-contract', 'Terms of Service', route('terms-of-service')],
 ['fa-cookie-bite', 'Cookie Policy', route('cookie-policy')],
 ] as [$ic,$label,$url])
 <a href="{{ $url }}" {{ str_starts_with($url,'http') ? 'target="_blank"' : '' }} style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:9px;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.6)'">
 <i class="fa-solid {{ $ic }}"></i>
 {{ $label }}
 </a>
 @endforeach
 </div>
 </div>

 {{-- Session pricing quick reference --}}
 <!-- <div style="border-top:1px solid rgba(255,255,255,.07);padding:24px 0;margin-bottom:0;">
 <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);margin-bottom:12px;">Session Pricing</p>
 <div style="display:flex;flex-wrap:wrap;gap:8px;">
 @foreach([['?','Quick Help','20 min','KSh 300'],['??','Guided Practice','40 min','KSh 600'],['','Deep Dive','60 min','KSh 900']] as [$ic,$name,$dur,$price])
 <a href="{{ route('tutors.index') }}" style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:8px 14px;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='rgba(255,255,255,.06)'">
 <span style="font-size:14px;">{{ $ic }}</span>
 <div>
 <div style="font-size:12px;font-weight:600;color:#fff;">{{ $name }}</div>
 <div style="font-size:11px;color:rgba(255,255,255,.45);">{{ $dur }}  <span style="color:#EEBE15;font-weight:700;">{{ $price }}</span></div>
 </div>
 </a>
 @endforeach
 </div>
 </div> -->
 </div>
 </div>

 {{-- Bottom bar --}}
 <div style="background:#0A1810;padding:16px 0;">
 <div style="max-width:1360px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
 <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
 <img src="/images/logo.svg" alt="Mwalimu" style="height:20px;width:auto;">
 <!-- <span style="font-size:11px;color:rgba(255,255,255,.35);">by <a href="https://ovaldesk.co.ke" target="_blank" style="color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">OvalDesk Ventures</a></span> -->
 </a>
 <div style="display:flex;align-items:center;gap:14px;">
 <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-scroll"></i> TSC Verified</span>
 <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-lock"></i> Secure</span>
 <span style="font-size:11px;color:rgba(255,255,255,.25);display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-shield-halved"></i> Verified</span>
 </div>
 </div>
 </div>
</footer>

{{-- Cookie Banner --}}
<div x-data="{ show: !localStorage.getItem('mw_cookie') }" x-show="show" x-cloak
 style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:460px;">
 <div style="background:#FFFDF5;border:2px solid #15281F;border-radius:22px;padding:18px 20px;box-shadow:0 16px 48px rgba(21,40,31,.2);">
 <div style="display:flex;gap:12px;align-items:flex-start;">
 <div style="width:38px;height:38px;border-radius:11px;background:rgba(212,160,23,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
 <i class="fa-solid fa-cookie-bite"></i>
 </div>
 <div style="flex:1;">
 <p style="font-weight:700;font-size:14px;color:#15281F;margin-bottom:3px;">We value your privacy</p>
 <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.55;">We use cookies to personalise your experience and keep your sessions secure on Mwalimu.</p>
 </div>
 </div>
 <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
 <button @click="localStorage.setItem('mw_cookie','min');show=false"
 style="padding:9px 18px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;color:#15281F;border:1.5px solid rgba(21,40,31,.2);cursor:pointer;font-family:inherit;transition:all .15s;"
 onmouseover="this.style.borderColor='rgba(21,40,31,.5)'" onmouseout="this.style.borderColor='rgba(21,40,31,.2)'">
 Necessary Only
 </button>
 <button @click="localStorage.setItem('mw_cookie','all');show=false"
 style="padding:9px 20px;border-radius:999px;font-size:13px;font-weight:700;background:#0A64BC;color:#fff;border:1.5px solid #15281F;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px;transition:all .18s;"
 onmouseover="this.style.background='#120A8F'" onmouseout="this.style.background='#0A64BC'">
 <i class="fa-solid fa-circle-check"></i> Accept All
 </button>
 </div>
 </div>
</div>

<style>
.mw-foot-grid{grid-template-columns:1fr!important;}
@media(min-width:640px){.mw-foot-grid{grid-template-columns:repeat(2,1fr)!important;}}
@media(min-width:1024px){.mw-foot-grid{grid-template-columns:2fr 1fr 1fr 1fr!important;}}
</style>

