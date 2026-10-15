<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" x-data="{ darkMode: false }" :class="{ dark: darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TutorLink') – Find Verified Tutors in Kenya</title>
    <meta name="description" content="@yield('description', 'Book verified 1:1 tutors across 80+ subjects. Choose 15, 30, 45 or 60-minute sessions and pay securely with Paystack.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-cream text-ink antialiased">

{{-- Announcement Bar --}}
<div x-data="{ show: true }" x-show="show" x-cloak
     class="relative z-50 bg-ink text-cream text-[13px] sm:text-sm overflow-hidden">
    <div class="max-w-site mx-auto px-2.5 sm:px-3 lg:px-4 py-1 sm:py-1.5 flex items-center justify-between gap-2">
        <span class="mx-auto">🎓 Find verified tutors across Kenya — book sessions in 15, 30, 45 or 60-minute blocks</span>
        <button type="button" @click="show = false"
                class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full hover:bg-white/10 transition text-cream"
                aria-label="Dismiss">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>
</div>

{{-- Navigation --}}
<header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-cream/85 backdrop-blur border-b border-[rgba(21,40,31,0.1)]">
    <div class="max-w-site mx-auto px-4 sm:px-5 lg:px-10 h-16 sm:h-[72px] flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0">
            <svg width="36" height="36" viewBox="0 0 100 100" aria-hidden="true">
                <circle cx="50" cy="50" r="50" fill="#15281F"/>
                <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
            </svg>
            <span class="font-serif text-xl font-semibold text-ink">TutorLink</span>
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden lg:flex items-center gap-1">
            <a href="{{ route('tutors.index') }}" class="px-4 py-2 rounded-full text-sm font-medium text-ink hover:bg-ink/5 transition">Find a Tutor</a>
            <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-full text-sm font-medium text-ink hover:bg-ink/5 transition">Courses</a>
            <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full text-sm font-medium text-ink hover:bg-ink/5 transition">Blog</a>
            <a href="#how-it-works" class="px-4 py-2 rounded-full text-sm font-medium text-ink hover:bg-ink/5 transition">How It Works</a>
        </nav>

        {{-- Right Side --}}
        <div class="flex items-center gap-2">
            @auth
                {{-- Notification Bell --}}
                @livewire('shared.notification-bell')

                {{-- User Menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full border border-[rgba(21,40,31,0.15)] hover:border-violet/40 transition">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="hidden sm:block text-sm font-medium text-ink max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="open ? 'rotate-180' : ''" class="transition-transform text-muted"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="open" x-transition x-cloak
                         class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl border border-hairline shadow-card-hover py-1.5 z-50">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink hover:bg-cream transition">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            Dashboard
                        </a>
                        @if(auth()->user()->isTutor())
                        <a href="{{ route('tutor.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink hover:bg-cream transition">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            My Profile
                        </a>
                        @endif
                        <div class="h-px bg-hairline mx-3 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-coral hover:bg-red-50 transition text-left">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-ink hover:bg-ink/5 rounded-full transition">Sign In</a>
                <a href="{{ route('register') }}" class="pill-btn-primary text-sm px-5 py-2.5">Get Started</a>
            @endauth

            {{-- Mobile Hamburger --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden w-10 h-10 rounded-full border-2 border-[rgba(21,40,31,0.2)] grid place-items-center hover:border-ink transition text-ink">
                <svg x-show="!mobileMenuOpen" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute left-0 right-0 top-full bg-cream border-b border-hairline shadow-lg lg:hidden px-4 py-4 flex flex-col gap-1">
        <a href="{{ route('tutors.index') }}" class="py-3 border-b border-hairline text-sm font-medium text-ink">Find a Tutor</a>
        <a href="{{ route('courses.index') }}" class="py-3 border-b border-hairline text-sm font-medium text-ink">Courses</a>
        <a href="{{ route('blog.index') }}" class="py-3 border-b border-hairline text-sm font-medium text-ink">Blog</a>
        <a href="#how-it-works" class="py-3 text-sm font-medium text-ink">How It Works</a>
    </div>
</header>

{{-- Flash Messages --}}
@if(session('success'))
<div class="max-w-site mx-auto px-4 mt-4">
    <div class="rounded-2xl bg-mint/10 border border-mint/30 text-mint px-4 py-3 text-sm font-medium flex items-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif
@if(session('error'))
<div class="max-w-site mx-auto px-4 mt-4">
    <div class="rounded-2xl bg-coral/10 border border-coral/30 text-coral px-4 py-3 text-sm font-medium">
        {{ session('error') }}
    </div>
</div>
@endif

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-ink text-cream mt-24">
    <div class="max-w-site mx-auto px-4 sm:px-5 lg:px-10 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10">
            {{-- Brand --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                    <svg width="32" height="32" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50" fill="#FBF5E6"/>
                        <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                        <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
                    </svg>
                    <span class="font-serif text-xl font-semibold text-cream">TutorLink</span>
                </a>
                <p class="text-cream/60 text-sm leading-relaxed max-w-xs">Connecting learners and parents with verified tutors across Kenya. Book 1:1 sessions in flexible time blocks.</p>
                <div class="flex items-center gap-3 mt-5">
                    <a href="#" class="w-9 h-9 rounded-full border border-cream/20 flex items-center justify-center text-cream/60 hover:text-cream hover:border-cream/50 transition" aria-label="Twitter/X">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full border border-cream/20 flex items-center justify-center text-cream/60 hover:text-cream hover:border-cream/50 transition" aria-label="Instagram">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full border border-cream/20 flex items-center justify-center text-cream/60 hover:text-cream hover:border-cream/50 transition" aria-label="Facebook">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-cream/40 mb-4">For Students</p>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('tutors.index') }}" class="text-sm text-cream/70 hover:text-cream transition">Find a Tutor</a></li>
                    <li><a href="{{ route('courses.index') }}" class="text-sm text-cream/70 hover:text-cream transition">Browse Courses</a></li>
                    <li><a href="{{ route('register') }}" class="text-sm text-cream/70 hover:text-cream transition">Sign Up Free</a></li>
                    <li><a href="#how-it-works" class="text-sm text-cream/70 hover:text-cream transition">How It Works</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-cream/40 mb-4">For Tutors</p>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('register') }}?role=tutor" class="text-sm text-cream/70 hover:text-cream transition">Become a Tutor</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Tutor Guidelines</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Earnings</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Verification Process</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-cream/40 mb-4">Support</p>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Help Centre</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Contact Us</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Privacy Policy</a></li>
                    <li><a href="#" class="text-sm text-cream/70 hover:text-cream transition">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-cream/10 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-cream/40">
            <span>&copy; {{ date('Y') }} TutorLink. All rights reserved.</span>
            <span>Made with ❤ for learners across Kenya</span>
        </div>
    </div>
</footer>

{{-- Cookie Banner --}}
<div id="cookie-banner-root"
     x-data="cookieBanner()"
     x-show="show"
     x-transition:enter="transition ease-out duration-250 transform"
     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-cloak
     style="position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:99999;width:calc(100% - 2rem);max-width:440px;"
     role="dialog" aria-label="Cookie consent">
    <div style="background:#FFFDF5;border:2px solid #15281F;border-radius:1.25rem;box-shadow:0 14px 40px rgba(21,40,31,0.22);padding:1.15rem;color:#15281F;font-family:'DM Sans',sans-serif;">
        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:0.75rem;background:#E8F5E9;border:1.5px solid #128C4B;color:#128C4B;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                🍪
            </div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.95rem;font-weight:800;">We value your privacy</span>
                    <button @click="rejectAll()" style="background:none;border:none;color:rgba(21,40,31,0.45);cursor:pointer;padding:0.25rem;">✕</button>
                </div>
                <p style="font-size:0.75rem;color:rgba(21,40,31,0.7);margin:0.3rem 0 0;">We use cookies to personalise your experience and improve the platform.</p>
            </div>
        </div>
        <div style="margin-top:0.85rem;padding-top:0.75rem;border-top:1.5px solid rgba(21,40,31,0.12);display:flex;align-items:center;justify-content:flex-end;gap:0.5rem;">
            <button @click="rejectAll()" style="padding:0.45rem 0.85rem;border-radius:0.65rem;font-size:0.75rem;font-weight:700;background:#fff;color:#15281F;border:1.5px solid #15281F;cursor:pointer;">Necessary Only</button>
            <button @click="acceptAll()" style="padding:0.45rem 1rem;border-radius:0.65rem;font-size:0.75rem;font-weight:800;background:#6D28D9;color:#fff;border:1.5px solid #15281F;cursor:pointer;">Accept All</button>
        </div>
    </div>
</div>

@livewireScripts
@stack('scripts')
<script>
function cookieBanner() {
    return {
        show: !localStorage.getItem('cookie_consent'),
        acceptAll()  { localStorage.setItem('cookie_consent','all');  this.show = false; },
        rejectAll()  { localStorage.setItem('cookie_consent','none'); this.show = false; },
    }
}
</script>
</body>
</html>
