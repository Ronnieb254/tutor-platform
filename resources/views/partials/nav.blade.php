@php $cr = request()->route()?->getName() ?? ''; @endphp

{{-- Announcement Bar --}}
<div x-data="{ ann: !sessionStorage.getItem('mw_ann') }" x-show="ann" x-cloak
     style="background:linear-gradient(90deg,#120A8F,#0A64BC);color:#fff;font-size:13px;padding:9px 20px;display:flex;align-items:center;justify-content:center;gap:10px;position:relative;z-index:101;">
    <i class="fa-solid fa-certificate fa-xs" style="opacity:.8;"></i>
    <span style="text-align:center;">All tutors are <strong>TSC-registered & background-checked</strong> — <strong>100% money-back</strong> on your first lesson</span>
    <button @click="sessionStorage.setItem('mw_ann','1');ann=false"
            style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;transition:background .15s;"
            onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'"
            aria-label="Dismiss">
        <i class="fa-solid fa-xmark fa-xs"></i>
    </button>
</div>

{{-- Main Navigation --}}
<nav style="position:sticky;top:0;z-index:100;background:rgba(251,245,230,.95);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1.5px solid rgba(21,40,31,.08);box-shadow:0 2px 20px rgba(21,40,31,.05);"
     x-data="{ open: false }">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px;">

            {{-- Logo --}}
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0;">
                <svg width="36" height="36" viewBox="0 0 100 100" style="flex-shrink:0;">
                    <circle cx="50" cy="50" r="50" fill="#15281F"/>
                    <circle cx="50" cy="50" r="34" fill="#0A64BC"/>
                    <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
                </svg>
                <span style="font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:#15281F;line-height:1;">
                    <span style="color:#0A64BC;">Mw</span>alimu
                </span>
            </a>

            {{-- Desktop Links --}}
            <div style="display:none;align-items:center;gap:2px;" class="mw-desklinks">
                @foreach([
                    ['tutors.index',       'fa-magnifying-glass', 'Find a Tutor'],
                    ['how-it-works',       'fa-circle-info',      'How It Works'],
                    ['examiner-clinic',    'fa-star',             'Examiner\'s Clinic'],
                    ['group-sessions.index','fa-users',           'Group Sessions'],
                    ['library.index',      'fa-book-open',        'Library'],
                    ['contact',            'fa-envelope',         'Contact'],
                ] as [$route, $icon, $label])
                <a href="{{ route($route) }}"
                   style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:999px;font-size:14px;font-weight:500;color:{{ Str::startsWith($cr,$route)||$cr===$route ? '#0A64BC' : '#15281F' }};background:{{ Str::startsWith($cr,$route)||$cr===$route ? 'rgba(10,100,188,.08)' : 'transparent' }};font-weight:{{ Str::startsWith($cr,$route)||$cr===$route ? '700' : '500' }};transition:all .15s;text-decoration:none;"
                   onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='{{ Str::startsWith($cr,$route)||$cr===$route ? 'rgba(10,100,188,.08)' : 'transparent' }}'">
                    <i class="fa-solid {{ $icon }}" style="font-size:12px;opacity:.7;"></i>
                    {{ $label }}
                </a>
                @endforeach
            </div>

            {{-- Right Actions --}}
            <div style="display:flex;align-items:center;gap:8px;">
                @auth
                    {{-- Notification Bell --}}
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    <div x-data="{ nopen: false }" style="position:relative;">
                        <button @click="nopen=!nopen" @click.outside="nopen=false"
                                style="position:relative;width:38px;height:38px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.borderColor='rgba(10,100,188,.4)'" onmouseout="this.style.borderColor='rgba(21,40,31,.12)'">
                            <i class="fa-solid fa-bell" style="font-size:14px;color:rgba(21,40,31,.6);"></i>
                            @if($unread > 0)
                            <span style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;background:#E8522A;border-radius:50%;font-size:9px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;border:2px solid var(--cream);">{{ min($unread,9) }}</span>
                            @endif
                        </button>
                        <div x-show="nopen" x-transition x-cloak
                             style="position:absolute;right:0;top:calc(100% + 8px);width:300px;background:#fff;border-radius:18px;border:1px solid rgba(21,40,31,.1);box-shadow:0 12px 40px rgba(21,40,31,.14);overflow:hidden;z-index:200;">
                            <div style="padding:14px 16px;border-bottom:1px solid rgba(21,40,31,.08);display:flex;align-items:center;justify-content:space-between;">
                                <span style="font-family:'Fraunces',serif;font-size:15px;font-weight:700;color:#15281F;">Notifications</span>
                                @if($unread>0)
                                <form method="POST" action="/tutor/notifications/mark-all-read" style="display:inline;">@csrf
                                    <button style="background:none;border:none;font-size:12px;color:#0A64BC;cursor:pointer;font-family:inherit;font-weight:600;">Mark all read</button>
                                </form>
                                @endif
                            </div>
                            <div style="max-height:280px;overflow-y:auto;">
                                @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $n)
                                @php $d=$n->data; @endphp
                                <div style="padding:12px 16px;border-bottom:1px solid rgba(21,40,31,.05);background:{{ is_null($n->read_at)?'rgba(10,100,188,.04)':'transparent' }};">
                                    <p style="font-size:13px;font-weight:{{ is_null($n->read_at)?'600':'400' }};color:#15281F;margin-bottom:3px;">{{ $d['message']??'New notification' }}</p>
                                    @if(!empty($d['code']))<p style="font-family:monospace;font-size:13px;font-weight:800;color:#0A64BC;background:rgba(10,100,188,.08);padding:2px 8px;border-radius:6px;display:inline-block;margin:3px 0;">{{ $d['code'] }}</p>@endif
                                    <p style="font-size:11px;color:rgba(21,40,31,.45);">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                                @empty
                                <div style="padding:24px;text-align:center;color:rgba(21,40,31,.45);font-size:13px;">No notifications yet</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- User Menu --}}
                    <div x-data="{ uopen: false }" style="position:relative;">
                        <button @click="uopen=!uopen" @click.outside="uopen=false"
                                style="display:flex;align-items:center;gap:8px;padding:5px 12px 5px 5px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.borderColor='rgba(10,100,188,.4)'" onmouseout="this.style.borderColor='rgba(21,40,31,.12)'">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            <span style="font-size:13px;font-weight:600;color:#15281F;display:none;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" class="mw-uname">{{ Str::limit(auth()->user()->name,15) }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size:10px;color:rgba(21,40,31,.5);" :style="uopen?'transform:rotate(180deg)':''" style="transition:.2s"></i>
                        </button>
                        <div x-show="uopen" x-transition x-cloak
                             style="position:absolute;right:0;top:calc(100% + 8px);width:200px;background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.1);box-shadow:0 8px 32px rgba(21,40,31,.12);padding:6px;z-index:200;">
                            <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:#15281F;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#FBF5E6'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-gauge-high" style="color:#0A64BC;width:14px;text-align:center;font-size:13px;"></i> Dashboard
                            </a>
                            <div style="height:1px;background:rgba(21,40,31,.08);margin:4px 0;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:13.5px;color:#E8522A;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;transition:background .15s;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='transparent'">
                                    <i class="fa-solid fa-right-from-bracket" style="width:14px;text-align:center;font-size:13px;"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="font-size:14px;font-weight:600;padding:8px 16px;border-radius:999px;color:#15281F;text-decoration:none;transition:background .15s;display:none;" class="mw-signin" onmouseover="this.style.background='rgba(21,40,31,.07)'" onmouseout="this.style.background='transparent'">Sign In</a>
                    <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='#120A8F';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#0A64BC';this.style.transform='translateY(0)'">
                        <i class="fa-solid fa-user-plus fa-xs"></i> Get Started
                    </a>
                @endauth

                {{-- Hamburger --}}
                <button @click="open=!open" class="mw-ham"
                        style="display:flex;width:40px;height:40px;border-radius:999px;border:1.5px solid rgba(21,40,31,.12);background:#fff;cursor:pointer;align-items:center;justify-content:center;color:#15281F;transition:all .15s;"
                        onmouseover="this.style.borderColor='rgba(10,100,188,.4)'" onmouseout="this.style.borderColor='rgba(21,40,31,.12)'"
                        aria-label="Menu">
                    <i x-show="!open" class="fa-solid fa-bars" style="font-size:15px;"></i>
                    <i x-show="open" x-cloak class="fa-solid fa-xmark" style="font-size:15px;"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="position:absolute;left:0;right:0;top:100%;background:rgba(251,245,230,.98);backdrop-filter:blur(20px);border-bottom:1.5px solid rgba(21,40,31,.08);padding:12px 20px 16px;z-index:99;box-shadow:0 12px 36px rgba(21,40,31,.12);">
            @foreach([
                ['tutors.index',       'fa-magnifying-glass', 'Find a Tutor'],
                ['how-it-works',       'fa-circle-info',      'How It Works'],
                ['examiner-clinic',    'fa-star',             'Examiner\'s Clinic'],
                ['group-sessions.index','fa-users',           'Group Sessions'],
                ['library.index',      'fa-book-open',        'Library'],
                ['contact',            'fa-envelope',         'Contact'],
            ] as [$route, $icon, $label])
            <a href="{{ route($route) }}"
               style="display:flex;align-items:center;gap:10px;padding:12px 0;font-size:15px;font-weight:500;color:#15281F;border-bottom:1px solid rgba(21,40,31,.07);text-decoration:none;">
                <i class="fa-solid {{ $icon }}" style="color:#0A64BC;width:16px;text-align:center;font-size:13px;"></i>
                {{ $label }}
            </a>
            @endforeach
            @guest
            <div style="display:flex;gap:8px;margin-top:12px;padding-top:12px;">
                <a href="{{ route('login') }}" style="flex:1;display:flex;align-items:center;justify-content:center;padding:11px;border-radius:999px;border:1.5px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">Sign In</a>
                <a href="{{ route('register') }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-user-plus fa-xs"></i> Get Started
                </a>
            </div>
            @endguest
        </div>
    </div>
</nav>

<style>
@media(min-width:1024px){
    .mw-desklinks{display:flex!important;}
    .mw-ham{display:none!important;}
    .mw-signin,.mw-uname{display:block!important;}
}
</style>
