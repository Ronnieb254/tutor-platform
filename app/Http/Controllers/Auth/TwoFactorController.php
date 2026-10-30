<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorAuth;
use App\Models\User;
use App\Notifications\TwoFactorEnabledNotification;
use App\Services\Google2FAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        $user = Auth::user();
        
        if (!$user && $request->session()->has('2fa_user_id')) {
            $user = User::find($request->session()->get('2fa_user_id'));
        }
        
        if (!$user || (!$user->isAdmin() && !$user->isTutor())) {
            return redirect()->route('dashboard');
        }

        $google2fa = app(Google2FAService::class);
        $secret = $google2fa->generateSecretKey();
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        
        // Generate QR code inline
        $qrCodeInline = $this->generateQRCodeSVG($qrCodeUrl);
        
        // Generate 8 recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        return view('auth.2fa-setup', compact('secret', 'qrCodeInline', 'recoveryCodes'));
    }

    public function showForcedSetup(Request $request)
    {
        $user = Auth::user();
        
        if (!$user && $request->session()->has('2fa_user_id')) {
            $user = User::find($request->session()->get('2fa_user_id'));
        }
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        $google2fa = app(Google2FAService::class);
        $secret = $google2fa->generateSecretKey();
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        
        $qrCodeInline = $this->generateQRCodeSVG($qrCodeUrl);
        
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        return view('auth.2fa-force', compact('secret', 'qrCodeInline', 'recoveryCodes'));
    }

    public function storeSetup(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
            'recovery_codes' => 'required|json',
        ]);

        $user = Auth::user();
        
        if (!$user && $request->session()->has('2fa_user_id')) {
            $user = User::find($request->session()->get('2fa_user_id'));
        }

        if (!$user || (!$user->isAdmin() && !$user->isTutor())) {
            return back()->withErrors(['error' => 'Unauthorized access.']);
        }

        $google2fa = app(Google2FAService::class);
        $valid = $google2fa->verifyKey($request->secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Store 2FA settings
        TwoFactorAuth::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $request->secret,
                'recovery_codes' => json_decode($request->recovery_codes, true),
                'enabled_at' => now(),
            ]
        );

        // Send notification
        $user->notify(new TwoFactorEnabledNotification());

        // Clear session and log in the user
        $request->session()->forget('2fa_user_id');
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Two-factor authentication has been enabled.');
    }

    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $user = User::find($request->session()->get('2fa_user_id'));

        if (!$user || !$user->twoFactorAuth) {
            return back()->withErrors(['code' => 'Two-factor authentication not configured.']);
        }

        $twoFactorAuth = $user->twoFactorAuth;
        $code = $request->code;

        // Check if it's a recovery code
        if (strlen($code) > 6 && strpos($code, '-') !== false) {
            $recoveryCodes = $twoFactorAuth->recovery_codes;
            $codeUpper = strtoupper($code);
            
            if (in_array($codeUpper, $recoveryCodes)) {
                // Remove used recovery code
                $recoveryCodes = array_values(array_filter($recoveryCodes, fn($c) => $c !== $codeUpper));
                $twoFactorAuth->update(['recovery_codes' => $recoveryCodes]);
                
                // Log in user
                $request->session()->forget('2fa_user_id');
                Auth::login($user);
                
                return redirect()->route('dashboard')->with('success', 'Logged in using recovery code.');
            } else {
                return back()->withErrors(['code' => 'Invalid recovery code.']);
            }
        }

        // Verify TOTP code
        $google2fa = app(Google2FAService::class);
        $valid = $google2fa->verifyKey($twoFactorAuth->secret, $code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Log in user
        $request->session()->forget('2fa_user_id');
        Auth::login($user);

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $user->twoFactorAuth()->delete();

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $user = Auth::user();

        if (!$user->twoFactorAuth) {
            return back()->withErrors(['error' => 'Two-factor authentication is not enabled.']);
        }

        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }

        $user->twoFactorAuth->update(['recovery_codes' => $recoveryCodes]);

        return back()->with('success', 'Recovery codes have been regenerated.')->with('recovery_codes', $recoveryCodes);
    }

    private function generateQRCodeSVG($url)
    {
        // Simple QR code generation using data URL
        // In production, you would use a proper QR code library
        // For now, we'll use a fallback to Google Charts API
        return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($url);
    }
}
