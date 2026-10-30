<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Wallet, TutorProfile, NotificationPreference};
use App\Services\{OtpService, PasswordHistoryService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth, DB};

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otp,
        protected PasswordHistoryService $passwordHistory
    ) {}

    /**
     * Show the OTP verification page.
     */
    public function show(Request $request)
    {
        if (!session('reg_pending')) {
            return redirect()->route('register')
                ->with('error', 'Session expired. Please register again.');
        }

        $pending = session('reg_pending');
        $email   = $pending['email'];
        $phone   = $pending['phone'] ?? null;

        return view('auth.verify-otp', compact('email', 'phone'));
    }

    /**
     * Verify OTPs and create the account.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email_otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'phone_otp' => ['nullable', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'email_otp.size'   => 'The email verification code must be exactly 6 digits.',
            'email_otp.regex'  => 'The email verification code must contain only digits.',
            'phone_otp.size'   => 'The phone verification code must be exactly 6 digits.',
            'phone_otp.regex'  => 'The phone verification code must contain only digits.',
        ]);

        $pending = session('reg_pending');
        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Session expired. Please register again.');
        }

        // Verify email OTP
        $emailResult = $this->otp->verify($pending['email'], 'email', $request->email_otp);
        if ($emailResult !== true) {
            return back()->withErrors(['email_otp' => $emailResult]);
        }

        // Verify phone OTP only if phone was provided
        if (!empty($pending['phone']) && !empty($request->phone_otp)) {
            $phoneResult = $this->otp->verify($pending['phone'], 'phone', $request->phone_otp);
            if ($phoneResult !== true) {
                return back()->withErrors(['phone_otp' => $phoneResult]);
            }
        }

        // Both verified — create account
        try {
            DB::beginTransaction();

            $user = User::create([
                'name'      => $pending['name'],
                'email'     => $pending['email'],
                'password'  => Hash::make($pending['password']),
                'role'      => $pending['role'],
                'phone'     => $pending['phone'] ?? null,
                'timezone'  => $pending['timezone'] ?? 'Africa/Nairobi',
                'is_active' => true,
                // Mark email as verified since they just proved ownership
                'email_verified_at' => now(),
            ]);

            Wallet::create(['user_id' => $user->id, 'balance' => 0]);

            if ($pending['role'] === 'tutor') {
                TutorProfile::create([
                    'user_id'             => $user->id,
                    'bio'                 => '',
                    'hourly_rate'         => 0,
                    'years_experience'    => 0,
                    'verification_status' => 'pending',
                    'is_available'        => false,
                ]);
            }

            NotificationPreference::create([
                'user_id'                => $user->id,
                'email_enabled'          => true,
                'booking_notifications'  => true,
                'message_notifications'  => true,
                'reminder_notifications' => true,
            ]);

            // Store initial password in history
            $this->passwordHistory->storePassword($user);

            DB::commit();

            // Clear session and log in
            session()->forget('reg_pending');

            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Welcome to Mwalimu! Your account has been verified.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Account creation after OTP failed: ' . $e->getMessage());
            return back()->withErrors(['email_otp' => 'Account creation failed. Please try again.']);
        }
    }

    /**
     * Resend OTP.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone'],
        ]);

        $pending = session('reg_pending');
        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please register again.'], 422);
        }

        $type       = $request->type;
        $identifier = $type === 'email' ? $pending['email'] : ($pending['phone'] ?? null);

        if (!$identifier) {
            return response()->json(['success' => false, 'message' => 'No phone number on file.'], 422);
        }

        try {
            $this->otp->send(
                $identifier, 
                $type, 
                $pending['name'],
                $pending['phone'] ?? ''
            );
            return response()->json([
                'success' => true,
                'message' => "A new code has been sent to your {$type}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to resend code. Please try again.'], 500);
        }
    }
}
