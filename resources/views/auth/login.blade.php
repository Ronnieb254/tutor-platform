@extends('layouts.guest')
@section('title', 'Sign In')

@section('content')
<div class="w-full max-w-md">
    <div class="card p-8 sm:p-10">
        <h1 class="font-serif text-3xl font-bold text-ink mb-1">Welcome back</h1>
        <p class="text-muted text-sm mb-8">Sign in to your TutorLink account</p>

        {{-- Social Login --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <a href="{{ route('auth.social.redirect', 'google') }}"
               class="flex items-center justify-center gap-2 h-11 rounded-2xl border border-hairline hover:border-violet/40 hover:bg-violet/5 transition text-sm font-medium text-ink">
                <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </a>
            <a href="{{ route('auth.social.redirect', 'facebook') }}"
               class="flex items-center justify-center gap-2 h-11 rounded-2xl border border-hairline hover:border-[#1877F2]/40 hover:bg-[#1877F2]/5 transition text-sm font-medium text-ink">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                Facebook
            </a>
            <a href="{{ route('auth.social.redirect', 'github') }}"
               class="flex items-center justify-center gap-2 h-11 rounded-2xl border border-hairline hover:border-ink/40 hover:bg-ink/5 transition text-sm font-medium text-ink">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub
            </a>
        </div>

        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-hairline"></div>
            <span class="text-xs text-muted">or sign in with email</span>
            <div class="flex-1 h-px bg-hairline"></div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-input @error('email') border-red-400 @enderror"
                       placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="form-label mb-0">Password</label>
                    <a href="#" class="text-xs text-violet hover:underline">Forgot password?</a>
                </div>
                <input type="password" name="password" class="form-input @error('password') border-red-400 @enderror"
                       placeholder="••••••••" autocomplete="current-password">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded text-violet accent-violet">
                <label for="remember" class="text-sm text-muted">Keep me signed in</label>
            </div>
            <button type="submit" class="w-full pill-btn-primary py-3 text-base mt-2">
                Sign In
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-muted mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-violet font-semibold hover:underline">Create one free</a>
    </p>
</div>
@endsection
