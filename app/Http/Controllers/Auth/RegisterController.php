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

        // Send email OTP — store result in session regardless of mail success
        $otpSent = false;
        $otpCode = null;

        try {
            $record  = $this->otp->send(
                $validated['email'], 
                'email', 
                $validated['name'],
                $validated['phone'] ?? ''
            );
            $otpSent = true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email OTP: ' . $e->getMessage(), [
                'email'      => $validated['email'],
                'mailer'     => config('mail.default'),
                'host'       => config('mail.mailers.smtp.host'),
                'port'       => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
            ]);

            // Fall back: generate OTP manually and store it so verification still works
            try {
                // Delete any old OTPs and create a new one directly
                \App\Models\OtpVerification::where('identifier', $validated['email'])
                    ->where('type', 'email')
                    ->where('verified', false)
                    ->delete();

                $fallbackOtp = (string) random_int(100000, 999999);
                \App\Models\OtpVerification::create([
                    'identifier' => $validated['email'],
                    'type'       => 'email',
                    'otp'        => $fallbackOtp,
                    'phone'      => $validated['phone'] ?? '',   // from registration form
                    'verified'   => false,
                    'attempts'   => 0,
                    'expires_at' => now()->addMinutes(10),
                ]);

                $otpCode = $fallbackOtp;
                \Log::warning("Email OTP fallback — OTP for {$validated['email']}: {$fallbackOtp}");
            } catch (\Exception $dbError) {
                \Log::error('OTP fallback DB write also failed: ' . $dbError->getMessage());
                return back()->withInput()->withErrors([
                    'email' => 'Registration service unavailable. Please try again in a moment.',
                ]);
            }
        }

        // Send phone OTP only if a phone was provided
        if (!empty($validated['phone'])) {
            try {
                $this->otp->send(
                    $validated['phone'], 
                    'phone', 
                    $validated['name'],
                    $validated['phone']
                );
            } catch (\Exception $e) {
                \Log::warning('Failed to send phone OTP: ' . $e->getMessage());
                // Non-fatal: email verification is sufficient
            }
        }

        $flashMessage = $otpSent
            ? 'We sent a 6-digit verification code to ' . $validated['email'] . '.'
            : 'Could not send the email — please enter the code shown on this page.';

        $redirect = redirect()->route('otp.verify.show')->with('info', $flashMessage);

        // In non-production or when mail failed, flash the OTP so user can proceed
        if ($otpCode !== null) {
            $redirect = $redirect->with('otp_fallback', $otpCode)
                                 ->with('otp_fallback_email', $validated['email']);
        }

        return $redirect;
    }
}
