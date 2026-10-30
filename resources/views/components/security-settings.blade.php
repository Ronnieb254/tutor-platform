@props(['user'])

@php
$has2FA = $user->has2FAEnabled();
@endphp

<div class="card card-p" style="margin-bottom:24px;">
    <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--hairline);">
        🔒 Two-Factor Authentication
    </h3>

    @if(session('success'))
    <div style="background:rgba(16,185,129,0.1);border-left:4px solid var(--mint);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;color:var(--ink);">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if(session('recovery_codes'))
    <div style="background:rgba(232,82,42,0.08);border-left:4px solid var(--coral);padding:16px;border-radius:8px;margin-bottom:20px;">
        <div style="font-weight:700;margin-bottom:12px;color:var(--coral);">Your New Recovery Codes</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
            @foreach(session('recovery_codes') as $code)
                <div style="font-family:monospace;font-size:13px;background:var(--cream);padding:10px;border-radius:6px;text-align:center;">{{ $code }}</div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline" onclick="copyRecoveryCodes()">📋 Copy All</button>
    </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <div style="font-weight:600;font-size:15px;color:var(--ink);margin-bottom:6px;">
                Status: 
                @if($has2FA)
                    <span style="color:var(--mint);">● Enabled</span>
                @else
                    <span style="color:var(--muted);">○ Disabled</span>
                @endif
            </div>
            <p style="font-size:13px;color:var(--muted);line-height:1.5;">
                @if($has2FA)
                    Your account is protected with two-factor authentication. You'll be prompted for a code when you sign in.
                @else
                    Add an extra layer of security to your account by requiring a code from your phone when you sign in.
                @endif
            </p>
        </div>
    </div>

    @if($has2FA)
        {{-- Enabled state - show disable option and recovery codes --}}
        <div style="display:flex;flex-direction:column;gap:12px;">
            <button type="button" onclick="toggleRecoveryCodes()" class="btn btn-outline btn-sm" style="width:fit-content;">
                🔑 View Recovery Codes
            </button>

            <div id="recovery-codes-section" style="display:none;background:var(--cream);padding:16px;border-radius:10px;margin-top:8px;">
                <div style="font-weight:600;margin-bottom:12px;font-size:14px;">Your Recovery Codes</div>
                @if($user->twoFactorAuth && $user->twoFactorAuth->recovery_codes)
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                        @foreach($user->twoFactorAuth->recovery_codes as $code)
                            <div style="font-family:monospace;font-size:13px;background:#fff;padding:10px;border-radius:6px;text-align:center;">{{ $code }}</div>
                        @endforeach
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="copyExistingRecoveryCodes()">📋 Copy</button>
                        <form action="{{ route('2fa.recovery') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline" onclick="return confirm('Generate new recovery codes? Your old codes will stop working.')">
                                🔄 Regenerate
                            </button>
                        </form>
                    </div>
                    <p style="font-size:12px;color:var(--muted);margin-top:10px;">Use these codes to access your account if you lose your phone.</p>
                @endif
            </div>

            <hr style="border:none;border-top:1px solid var(--hairline);margin:8px 0;">

            <form action="{{ route('2fa.disable') }}" method="POST" onsubmit="return confirm('Are you sure you want to disable two-factor authentication?')">
                @csrf
                <div style="margin-bottom:12px;">
                    <label class="form-label">Enter your password to disable 2FA</label>
                    <input type="password" name="password" class="form-input" placeholder="Your password" required>
                    @error('password')
                        <span style="display:block;margin-top:6px;font-size:13px;color:var(--coral);">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-sm" style="background:var(--coral);color:#fff;">
                    Disable Two-Factor Authentication
                </button>
            </form>
        </div>
    @else
        {{-- Disabled state - show enable option --}}
        <a href="{{ route('2fa.setup') }}" class="btn btn-primary" style="display:inline-block;width:fit-content;">
            ✓ Enable Two-Factor Authentication
        </a>
    @endif
</div>

<script>
function toggleRecoveryCodes() {
    const section = document.getElementById('recovery-codes-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function copyRecoveryCodes() {
    const codes = @json(session('recovery_codes', []));
    if (codes.length > 0) {
        const text = codes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('✓ Recovery codes copied to clipboard!');
        }).catch(() => {
            alert('Failed to copy. Please copy manually.');
        });
    }
}

function copyExistingRecoveryCodes() {
    @if($user->twoFactorAuth && $user->twoFactorAuth->recovery_codes)
        const codes = @json($user->twoFactorAuth->recovery_codes);
        const text = codes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('✓ Recovery codes copied to clipboard!');
        }).catch(() => {
            alert('Failed to copy. Please copy manually.');
        });
    @endif
}
</script>
