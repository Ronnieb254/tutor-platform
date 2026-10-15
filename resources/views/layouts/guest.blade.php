<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TutorLink')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-cream text-ink antialiased min-h-screen">
    <div class="min-h-screen flex flex-col">
        {{-- Simple header --}}
        <header class="px-6 py-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 w-fit">
                <svg width="32" height="32" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" fill="#15281F"/>
                    <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                    <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
                </svg>
                <span class="font-serif text-xl font-semibold text-ink">TutorLink</span>
            </a>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 py-10">
            @yield('content')
        </main>

        <footer class="text-center text-xs text-muted py-6">
            &copy; {{ date('Y') }} TutorLink &middot;
            <a href="#" class="hover:text-ink transition">Privacy</a> &middot;
            <a href="#" class="hover:text-ink transition">Terms</a>
        </footer>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
