<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mwalimu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @livewireStyles
    <style>
        :root { --ink:#15281F; --cream:#FBF5E6; --violet:#6D28D9; --coral:#E8522A; --gold:#D4A017; --mint:#10B981; --muted:rgba(21,40,31,0.55); --hairline:rgba(21,40,31,0.1); }
        *,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
        html { background:var(--cream); }
        body { font-family:'DM Sans',sans-serif; color:var(--ink); background:var(--cream); line-height:1.6; min-height:100vh; display:flex; flex-direction:column; }
        h1,h2,h3 { font-family:'Fraunces',Georgia,serif; }
        a { color:inherit; text-decoration:none; }
        [x-cloak] { display:none !important; }

        .guest-header { padding:24px 32px; }
        .guest-logo { display:inline-flex; align-items:center; gap:10px; font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:var(--ink); }
        .guest-main { flex:1; display:flex; align-items:center; justify-content:center; padding:20px 16px 40px; }
        .guest-footer { text-align:center; padding:20px; font-size:12px; color:var(--muted); }
        .guest-footer a { color:var(--violet); }

        /* Auth card */
        .auth-card { background:#fff; border-radius:24px; border:1px solid var(--hairline); box-shadow:0 4px 32px rgba(21,40,31,0.08); padding:40px; width:100%; max-width:440px; }
        @media(max-width:480px) { .auth-card { padding:28px 20px; } }
        .auth-card h1 { font-size:30px; color:var(--ink); margin-bottom:6px; }
        .auth-card .subtitle { color:var(--muted); font-size:15px; margin-bottom:28px; }

        /* Social buttons */
        .social-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:20px; }
        .social-btn { display:flex; align-items:center; justify-content:center; gap:6px; height:44px; border-radius:14px; border:1.5px solid var(--hairline); background:#fff; font-size:13px; font-weight:500; color:var(--ink); cursor:pointer; transition:border-color .15s; text-decoration:none; }
        .social-btn:hover { border-color:rgba(109,40,217,0.4); }

        /* Divider */
        .auth-divider { display:flex; align-items:center; gap:12px; margin:16px 0; }
        .auth-divider::before, .auth-divider::after { content:''; flex:1; height:1px; background:var(--hairline); }
        .auth-divider span { font-size:12px; color:var(--muted); }

        /* Form fields */
        .field { margin-bottom:16px; }
        .field label { display:block; font-size:13px; font-weight:600; color:var(--ink); margin-bottom:6px; }
        .field input, .field select, .field textarea { width:100%; height:48px; padding:0 16px; border-radius:14px; border:1.5px solid rgba(21,40,31,0.2); background:#fff; font-size:14px; font-family:inherit; color:var(--ink); outline:none; transition:border-color .15s; }
        .field input:focus, .field select:focus { border-color:var(--violet); box-shadow:0 0 0 3px rgba(109,40,217,0.1); }
        .field-error { font-size:12px; color:var(--coral); margin-top:4px; }
        .field-note { font-size:12px; color:var(--muted); margin-top:4px; }

        /* Submit button */
        .btn-submit { width:100%; height:50px; border-radius:999px; background:var(--violet); color:#fff; font-size:15px; font-weight:700; font-family:inherit; border:none; cursor:pointer; transition:background .15s; margin-top:8px; }
        .btn-submit:hover { background:#4C1D95; }

        /* Role selector */
        .role-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
        .role-option input { display:none; }
        .role-label { display:flex; flex-direction:column; align-items:center; gap:4px; padding:14px 8px; border-radius:16px; border:2px solid var(--hairline); cursor:pointer; transition:all .15s; }
        .role-option input:checked + .role-label { border-color:var(--violet); background:rgba(109,40,217,0.06); }
        .role-label span { font-size:24px; }
        .role-label em { font-size:12px; font-weight:600; font-style:normal; color:var(--ink); }
        .role-info { background:rgba(109,40,217,0.06); border:1px solid rgba(109,40,217,0.2); border-radius:14px; padding:14px; font-size:13px; color:var(--violet); margin-bottom:12px; display:none; }
        .role-info.show { display:block; }

        .auth-footer-text { text-align:center; font-size:14px; color:var(--muted); margin-top:20px; }
        .auth-footer-text a { color:var(--violet); font-weight:600; }
    </style>
</head>
<body>
    <header class="guest-header">
        <a href="{{ route('home') }}" class="guest-logo">
            <svg width="32" height="32" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="50" fill="#15281F"/>
                <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
            </svg>
            Mwalimu
        </a>
    </header>

    <main class="guest-main">
        @yield('content')
    </main>

    <footer class="guest-footer">
        &copy; {{ date('Y') }} Mwalimu &middot;
        <a href="#">Privacy</a> &middot; <a href="#">Terms</a>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
