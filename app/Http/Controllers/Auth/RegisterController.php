<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Wallet, TutorProfile, NotificationPreference};
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth, DB};
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(protected OtpService $otp) {}

    public function show()
    {
        return view('auth.register');
    }

    /**
     * Step 1: Validate input, store in session, send OTPs, redirect to verify page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => ['required', 'in:student,tutor,parent'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ]);

        // Store registration data in session — not yet in the DB
        session([
            'reg_pending' => [
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],  // plain-text; hashed on final create
                'role'     => $validated['role'],
                'phone'    => $validated['phone'] ?? null,
                'timezone' => $validated['timezone'] ?? 'Africa/Nairobi',
            ],
        ]);

        // Send email OTP
        try {
            $this->otp->send($validated['email'], 'email', $validated['name']);
        } catch (\Exception $e) {
            \Log::error('Failed to send email OTP: ' . $e->getMessage());

            // If mail is misconfigured (e.g. mailpit in production), fall back to
            // logging the OTP and still proceeding so registration isn't blocked.
            if (app()->environment('production') && config('app.debug') === false) {
                return back()->withInput()->withErrors(['email' => 'Could not send verification email. Please check your email address or try again later.']);
            }
            // In dev / non-production: still redirect so flow can be tested
        }

        // Send phone OTP only if a phone was provided
        if (!empty($validated['phone'])) {
            try {
                $this->otp->send($validated['phone'], 'phone', $validated['name']);
            } catch (\Exception $e) {
                \Log::warning('Failed to send phone OTP: ' . $e->getMessage());
                // Non-fatal: continue with email-only verification
            }
        }

        return redirect()->route('otp.verify.show')
            ->with('info', 'We sent a 6-digit verification code to ' . $validated['email'] . '.');
    }
}
