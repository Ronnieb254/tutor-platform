<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been deactivated. Please contact support.',
                    ], 403);
                }

                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            // 2FA Check for Admins and Tutors
            if ($user->isAdmin() || $user->isTutor()) {
                // Admin without 2FA - force setup
                if ($user->isAdmin() && !$user->has2FAEnabled()) {
                    $request->session()->put('2fa_user_id', $user->id);
                    Auth::logout();
                    return redirect()->route('2fa.setup.force');
                }
                
                // User with 2FA enabled - redirect to challenge
                if ($user->has2FAEnabled()) {
                    $request->session()->put('2fa_user_id', $user->id);
                    Auth::logout();
                    return redirect()->route('2fa.challenge');
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Welcome back, ' . $user->name . '!',
                    'redirect' => route('dashboard'),
                    'user' => $user,
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login');
    }
}
