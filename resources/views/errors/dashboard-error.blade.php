<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Error – Mwalimu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #15281F;
            --cream: #FBF5E6;
            --trust-blue: #0A64BC;
            --coral: #E8522A;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #F2F5F2 0%, #E8F0E8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 48px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(21, 40, 31, 0.1);
        }
        .error-icon {
            font-size: 72px;
            margin-bottom: 24px;
        }
        h1 {
            font-family: 'Fraunces', serif;
            font-size: 28px;
            color: var(--ink);
            margin-bottom: 12px;
        }
        p {
            font-size: 15px;
            color: rgba(21, 40, 31, 0.65);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--trust-blue);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn:hover {
            background: #084a8f;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: white;
            color: var(--ink);
            border: 2px solid #E0E4E0;
            margin-left: 8px;
        }
        .btn-secondary:hover {
            background: #F8FAF8;
            border-color: var(--trust-blue);
        }
        .error-code {
            font-size: 11px;
            color: rgba(21, 40, 31, 0.4);
            margin-top: 24px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>Dashboard Temporarily Unavailable</h1>
        <p>
            {{ $message ?? 'We\'re having trouble loading your dashboard right now. Our team has been notified and is working on it.' }}
        </p>
        <div>
            <a href="{{ route('home') }}" class="btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Go Home
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                Try Again
            </a>
        </div>
        @if(auth()->check())
        <div class="error-code">
            Session ID: {{ session()->getId() }} | User: {{ auth()->id() }}
        </div>
        @endif
    </div>
</body>
</html>
