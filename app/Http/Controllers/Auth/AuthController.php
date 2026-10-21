<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'This account has been deactivated. Please contact support.'])->withInput();
            }
            $request->session()->regenerate();
            return $this->redirectAfterLogin($user);
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
                'role'     => 'required|in:student,tutor,parent',
                'phone'    => 'nullable|string|max:30',
            ]);

            // Create user
            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => $validated['password'],
                'role'              => $validated['role'],
                'phone'             => $validated['phone'] ?? null,
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);

            // Create wallet for tutors
            if ($user->role === 'tutor') {
                Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'currency' => 'KES']
                );
            }

            // Login user
            Auth::login($user);

            // Redirect based on role
            if ($user->role === 'tutor') {
                // Check if route exists
                if (\Route::has('tutor.onboarding')) {
                    return redirect()->route('tutor.onboarding');
                }
                return redirect('/dashboard')->with('success', 'Registration successful! Please complete your tutor profile.');
            }

            return redirect()->route('dashboard');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so they display properly
            throw $e;
        } catch (\Exception $e) {
            // Log the full error
            \Log::error('Registration Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return with specific error
            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                'email' => 'Registration failed: ' . $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectAfterLogin(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->isAdmin())   return redirect()->route('admin.dashboard');
        if ($user->isTutor())   return redirect()->route('tutor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
