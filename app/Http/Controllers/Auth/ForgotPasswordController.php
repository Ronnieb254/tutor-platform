<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset link request form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;

        // Rate limiting: max 3 requests per hour per email
        $key = 'password-reset:' . $email;
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $availableIn = RateLimiter::availableIn($key);
            $minutes = ceil($availableIn / 60);
            
            return back()
                ->withErrors([
                    'email' => "Too many password reset attempts. Please try again in {$minutes} minute(s)."
                ])
                ->withInput();
        }

        // Attempt to send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Record this attempt for rate limiting (expires in 3600 seconds = 1 hour)
            RateLimiter::hit($key, 3600);

            return back()->with('status', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput();
    }
}
