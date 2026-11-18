@php $cr = request()->route()?->getName() ?? ''; @endphp

{{-- Announcement Bar --}}
<div x-data="{ ann: !sessionStorage.getItem('mw_ann') }" x-show="ann" x-cloak
 style="background:linear-gradient(90deg,#120A8F,#0A64BC);color:#fff;font-size:13px;padding:9px 20px;display:flex;align-items:center;justify-content:center;gap:10px;position:relative;z-index:101;">
 <i class="fa-solid fa-scroll"></i>
 <span style="text-align:center;line-height:1.4;">All tutors are <strong>TSC-registered &amp; background-checked</strong> &mdash; <strong>100% money-back</strong> if unsatisfied with your first lesson</span>
 <button @click="sessionStorage.setItem('mw_ann','1');ann=false"
 style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;"
 aria-label="Dismiss">
 <i class="fa-solid fa-xmark"></i>
 </button>
</div>

{{-- Main Navigation --}}
<nav style="position:sticky;top:0;z-index:100;background:rgba(251,245,230,.96);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1.5px solid rgba(21,40,31,.08);box-shadow:0 2px 20px rgba(21,40,31,.05);"
 x-data="{ open: false }">

 <div style="max-width:1360px;margin:0 auto;padding:0 16px;">
 <div style="display:flex;align-items:center;justify-content:space-between;height:64px;gap:8px;">

 {{-- Logo --}}
 <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;flex-shrink:0;">
 <img src="/images/logo.svg" alt="Mwalimu" style="height:34px;width:auto;">
 </a>

 {{-- Desktop nav links (hidden on mobile) --}}
 <div class="mw-desklinks" style="display:none;align-items:center;gap:2px;flex:1;justify-content:center;">
 @foreach([
 ['tutors.index', 'fa-magnifying-glass', 'Find a Tutor'],
 ['how-it-works', 'fa-circle-info', 'How It Works'],
 ['examiner-clinic', 'fa-star', "Examiner's Clinic"],
 ['group-sessions.index','fa-users', 'Group Sessions'],
 ['library.index', 'fa-book-open', 'Library'],
 ['contact', 'fa-envelope', 'Contact'],
 ] as [$route, $icon, $label])
 <a href="{{ route($route) }}"
 style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:999px;font-size:13.5px;color:{{ Str::startsWith($cr,$route)||$cr===$route ? '#0A64BC' : '#15281F' }};background:{{ Str::startsWith($cr,$route)||$cr===$route ? 'rgba(10,100,188,.08)' : 'transparent' }};font-weight:{{ Str::startsWith($cr,$route)||$cr===$route ? '700' : '500' }};transition:all .15s;text-decoration:none;white-space:nowrap;"
 onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='{{ Str::startsWith($cr,$route)||$cr===$route ? 'rgba(10,100,188,.08)' : 'transparent' }}'">
 <i class="fa-solid {{ $icon }}"></i>{{ $label }}
 </a>
 @endforeach
 </div>

 {{-- Right actions --}}
 <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">

 {{-- Curriculum switcher (desktop: full label; mobile: icon only) — COMMENTED OUT
 <div x-data="{ csopen: false }" style="position:relative;">
 <button @click="csopen=!csopen" @click.outside="csopen=false"
 class="mw-curr-btn"
 style="display:flex;align-items:center;gap:5px;padding:7px 10px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;cursor:pointer;font-size:13px;font-weight:600;color:#15281F;white-space:nowrap;"
 aria-label="Switch curriculum">
 @if(($curriculum_variant ?? 'kenyan') === 'international')
 <i class="fa-solid fa-globe" style="color:#6366F1;"></i>
 <span class="mw-curr-label" style="color:#6366F1;">International</span>
 @else
 <i class="fa-solid fa-flag" style="color:#10B981;"></i>
 <span class="mw-curr-label">Kenyan</span>
 @endif
 <i class="fa-solid fa-chevron-down" style="font-size:9px;color:rgba(21,40,31,.4);" class="mw-curr-label"></i>
 </button>
 <div x-show="csopen" x-transition x-cloak
 style="position:absolute;right:0;top:calc(100% + 8px);width:190px;background:#fff;border-radius:14px;border:1px solid rgba(21,40,31,.1);box-shadow:0 8px 24px rgba(21,40,31,.14);padding:8px;z-index:300;">
 <button @click="CurriculumSwitcher.switchVariant('kenyan');csopen=false" data-curriculum-switch="kenyan"
 style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:none;cursor:pointer;font-family:inherit;font-size:13px;color:#15281F;font-weight:600;text-align:left;transition:background .15s;margin-bottom:4px;background:{{ ($curriculum_variant ?? 'kenyan') === 'kenyan' ? 'rgba(16,185,129,.1)' : 'rgba(21,40,31,.04)' }};">
 <i class="fa-solid fa-flag" style="color:#10B981;width:16px;"></i>
 Kenyan CBC
 @if(($curriculum_variant ?? 'kenyan') === 'kenyan')
 <i class="fa-solid fa-check" style="margin-left:auto;color:#10B981;font-size:11px;"></i>
 @endif
 </button>
 <button @click="CurriculumSwitcher.switchVariant('international');csopen=false" data-curriculum-switch="international"
 style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:none;cursor:pointer;font-family:inherit;font-size:13px;color:#15281F;font-weight:600;text-align:left;transition:background .15s;background:{{ ($curriculum_variant ?? 'kenyan') === 'international' ? 'rgba(99,102,241,.1)' : 'rgba(21,40,31,.04)' }};">
 <i class="fa-solid fa-globe" style="color:#6366F1;width:16px;"></i>
 International
 @if(($curriculum_variant ?? 'kenyan') === 'international')
 <i class="fa-solid fa-check" style="margin-left:auto;color:#6366F1;font-size:11px;"></i>
 @endif
 </button>
 </div>
 </div>
 --}}

 @auth
 {{-- Notification Bell --}}
 @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
 <div x-data="{ nopen: false }" style="position:relative;">
 <button @click="nopen=!nopen" @click.outside="nopen=false"
 style="position:relative;width:38px;height:38px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;"
 aria-label="Notifications">
 <i class="fa-solid fa-bell" style="font-size:14px;"></i>
 @if($unread > 0)
 <span style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;background:#E8522A;border-radius:50%;font-size:9px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;border:2px solid #FBF5E6;">{{ min($unread,9) }}</span>
 @endif
 </button>
 <div x-show="nopen" x-transition x-cloak
 style="position:absolute;right:0;top:calc(100% + 8px);width:300px;max-width:calc(100vw - 24px);background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.1);box-shadow:0 12px 40px rgba(21,40,31,.14);overflow:hidden;z-index:300;">
 <div style="padding:14px 16px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;">
 <span style="font-family:'Fraunces',serif;font-size:15px;font-weight:700;color:#15281F;">Notifications</span>
 @if($unread > 0)
 <form method="POST" action="/tutor/notifications/mark-all-read" style="display:inline;">@csrf
 <button style="background:none;border:none;font-size:12px;color:#0A64BC;cursor:pointer;font-family:inherit;font-weight:600;">Mark all read</button>
 </form>
 @endif
 </div>
 <div style="max-height:280px;overflow-y:auto;">
 @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $n)
 @php $d = $n->data; @endphp
 <div style="padding:12px 16px;border-bottom:1px solid rgba(21,40,31,.05);background:{{ is_null($n->read_at) ? 'rgba(10,100,188,.04)' : 'transparent' }};">
 <p style="font-size:13px;font-weight:{{ is_null($n->read_at) ? '600' : '400' }};color:#15281F;margin-bottom:3px;">{{ $d['message'] ?? 'New notification' }}</p>
 @if(!empty($d['code']))<p style="font-family:monospace;font-size:13px;font-weight:800;color:#0A64BC;background:rgba(10,100,188,.08);padding:2px 8px;border-radius:6px;display:inline-block;margin:3px 0;">{{ $d['code'] }}</p>@endif
 <p style="font-size:11px;color:rgba(21,40,31,.45);">{{ $n->created_at->diffForHumans() }}</p>
 </div>
 @empty
 <div style="padding:24px;text-align:center;color:rgba(21,40,31,.45);font-size:13px;">No notifications yet</div>
 @endforelse
 </div>
 </div>
 </div>

 {{-- User menu (desktop only — mobile uses hamburger) --}}
 <div x-data="{ uopen: false }" class="mw-usermenu" style="display:none;position:relative;">
 <button @click="uopen=!uopen" @click.outside="uopen=false"
 style="display:flex;align-items:center;gap:7px;padding:4px 10px 4px 4px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;cursor:pointer;"
 aria-label="Account">
 <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;">
 <span style="font-size:13px;font-weight:600;color:#15281F;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit(auth()->user()->name, 12) }}</span>
 <i class="fa-solid fa-chevron-down" style="font-size:10px;color:rgba(21,40,31,.45);"></i>
 </button>
 <div x-show="uopen" x-transition x-cloak
 style="position:absolute;right:0;top:calc(100% + 8px);width:200px;background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.1);box-shadow:0 8px 32px rgba(21,40,31,.12);padding:6px;z-index:300;">
 <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:#15281F;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#FBF5E6'" onmouseout="this.style.background='transparent'">
 <i class="fa-solid fa-chart-bar"></i> Dashboard
 </a>
 <div style="height:1px;background:rgba(21,40,31,.08);margin:4px 0;"></div>
 <form method="POST" action="{{ route('logout') }}">@csrf
 <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:#E8522A;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='transparent'">
 <i class="fa-solid fa-right-from-bracket"></i> Sign Out
 </button>
 </form>
 </div>
 </div>
 </div>
 @else
 {{-- Desktop auth CTA --}}
 <a href="{{ route('login') }}" class="mw-signin" style="display:none;font-size:13.5px;font-weight:600;padding:8px 14px;border-radius:999px;color:#15281F;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">Sign In</a>
 <a href="{{ route('register') }}" class="mw-getstarted" style="display:none;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;background:#0A64BC;color:#fff;font-size:13.5px;font-weight:600;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background='#120A8F'" onmouseout="this.style.background='#0A64BC'">
 <i class="fa-solid fa-user"></i> Get Started
 </a>
 @endauth

 {{-- Hamburger (mobile only) --}}
 <button @click="open=!open" class="mw-ham"
 style="display:flex;width:40px;height:40px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;cursor:pointer;align-items:center;justify-content:center;color:#15281F;flex-shrink:0;"
 aria-label="Toggle menu" aria-expanded="false" :aria-expanded="open.toString()">
 <i x-show="!open" class="fa-solid fa-bars" style="font-size:15px;"></i>
 <i x-show="open" x-cloak class="fa-solid fa-xmark" style="font-size:15px;"></i>
 </button>
 </div>
 </div>

 {{-- Mobile Drawer --}}
 <div x-show="open" x-cloak @click.outside="open=false"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 -translate-y-2"
 x-transition:enter-end="opacity-100 translate-y-0"
 x-transition:leave="transition ease-in duration-150"
 x-transition:leave-start="opacity-100 translate-y-0"
 x-transition:leave-end="opacity-0 -translate-y-2"
 style="position:absolute;left:0;right:0;top:100%;background:rgba(251,245,230,.98);backdrop-filter:blur(20px);border-bottom:1.5px solid rgba(21,40,31,.08);box-shadow:0 16px 40px rgba(21,40,31,.14);z-index:99;max-height:calc(100vh - 64px);overflow-y:auto;">

 <div style="padding:8px 16px 20px;">

 {{-- Nav links --}}
 @foreach([
 ['tutors.index', 'fa-magnifying-glass', 'Find a Tutor'],
 ['how-it-works', 'fa-circle-info', 'How It Works'],
 ['examiner-clinic', 'fa-star', "Examiner's Clinic"],
 ['group-sessions.index','fa-users', 'Group Sessions'],
 ['library.index', 'fa-book-open', 'Library'],
 ['contact', 'fa-envelope', 'Contact'],
 ] as [$route, $icon, $label])
 <a href="{{ route($route) }}" @click="open=false"
 style="display:flex;align-items:center;gap:12px;padding:14px 8px;font-size:15px;font-weight:{{ Str::startsWith($cr,$route)||$cr===$route ? '700' : '500' }};color:{{ Str::startsWith($cr,$route)||$cr===$route ? '#0A64BC' : '#15281F' }};border-bottom:1px solid rgba(21,40,31,.07);text-decoration:none;">
 <i class="fa-solid {{ $icon }}" style="width:18px;text-align:center;color:{{ Str::startsWith($cr,$route)||$cr===$route ? '#0A64BC' : 'rgba(21,40,31,.4)' }};"></i>
 {{ $label }}
 @if(Str::startsWith($cr,$route)||$cr===$route)
 <i class="fa-solid fa-chevron-right" style="margin-left:auto;font-size:11px;color:#0A64BC;"></i>
 @endif
 </a>
 @endforeach

 @auth
 {{-- Dashboard shortcut --}}
 <a href="{{ route('dashboard') }}" @click="open=false"
 style="display:flex;align-items:center;gap:12px;padding:14px 8px;font-size:15px;font-weight:500;color:#15281F;border-bottom:1px solid rgba(21,40,31,.07);text-decoration:none;">
 <i class="fa-solid fa-chart-bar" style="width:18px;text-align:center;color:rgba(21,40,31,.4);"></i>
 My Dashboard
 </a>

 {{-- Mobile user info + sign out --}}
 <div style="display:flex;align-items:center;gap:12px;padding:14px 8px;border-bottom:1px solid rgba(21,40,31,.07);">
 <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
 <div style="flex:1;min-width:0;">
 <div style="font-size:14px;font-weight:700;color:#15281F;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
 <div style="font-size:12px;color:rgba(21,40,31,.5);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->email }}</div>
 </div>
 </div>

 <form method="POST" action="{{ route('logout') }}" style="padding:12px 8px 0;">
 @csrf
 <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:12px;background:#FEF2F2;color:#E8522A;border:1.5px solid rgba(232,82,42,.2);font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
 <i class="fa-solid fa-right-from-bracket"></i> Sign Out
 </button>
 </form>
 @else
 {{-- Guest auth buttons in mobile menu --}}
 <div style="display:flex;gap:10px;padding:16px 8px 0;">
 <a href="{{ route('login') }}" @click="open=false"
 style="flex:1;display:flex;align-items:center;justify-content:center;padding:13px;border-radius:12px;border:1.5px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
 Sign In
 </a>
 <a href="{{ route('register') }}" @click="open=false"
 style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:13px;border-radius:12px;background:#0A64BC;color:#fff;font-size:14px;font-weight:700;text-decoration:none;">
 <i class="fa-solid fa-user"></i> Get Started
 </a>
 </div>
 @endauth

 </div>
 </div>
 </div>
</nav>

<style>
/* ── Desktop: show links, user menu, sign-in; hide hamburger ─────────── */
@media(min-width:1024px){
 .mw-desklinks { display:flex !important; }
 .mw-ham { display:none !important; }
 .mw-usermenu { display:block !important; }
 .mw-signin { display:inline-flex !important; }
 .mw-getstarted { display:inline-flex !important; }
}

/* ── Tablet: show curriculum label, hide long text if needed ─────────── */
@media(min-width:640px) and (max-width:1023px){
 .mw-curr-label { display:inline !important; }
}

/* ── Mobile: hide curriculum text, keep icon only ────────────────────── */
@media(max-width:639px){
 .mw-curr-label { display:none !important; }
 .mw-curr-btn { padding:7px 9px !important; }
 .mw-ann-bar { padding-right:48px; font-size:12px; }
}
</style>
