<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Wallet, TutorProfile, NotificationPreference};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth, DB};
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:student,tutor,parent'],
            'phone' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'timezone' => $validated['timezone'] ?? 'Africa/Nairobi',
                'is_active' => true,
            ]);

            // Create wallet for all users
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            // Create tutor profile if role is tutor
            if ($validated['role'] === 'tutor') {
                TutorProfile::create([
                    'user_id' => $user->id,
                    'bio' => '',
                    'hourly_rate' => 0,
                    'years_experience' => 0,
                    'verification_status' => 'pending',
                    'is_available' => false,
                ]);
            }

            // Create notification preferences
            NotificationPreference::create([
                'user_id' => $user->id,
                'email_enabled' => true,
                'booking_notifications' => true,
                'message_notifications' => true,
                'reminder_notifications' => true,
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to the platform.',
                    'redirect' => route('dashboard'),
                    'user' => $user,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Welcome to the platform!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration failed: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}
