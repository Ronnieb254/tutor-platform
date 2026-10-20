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
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
                'role'     => 'required|in:student,tutor,parent',
                'phone'    => 'nullable|string|max:30',
            ]);

            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => $request->password,
                'role'              => $request->role,
                'phone'             => $request->phone,
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);

            if ($user->isTutor()) {
                Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'currency' => 'KES']
                );
            }

            Auth::login($user);

            if ($user->isTutor()) {
                return redirect()->route('tutor.onboarding');
            }

            return redirect()->route('dashboard');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so they display properly
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->withErrors([
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
