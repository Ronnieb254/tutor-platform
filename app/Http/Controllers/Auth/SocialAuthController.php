<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Social login failed. Please try again.']);
        }

        $user = User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'        => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar'      => $socialUser->getAvatar(),
                'email_verified_at' => now(),
                'password'    => null,
            ]
        );

        // Create wallet if tutor
        if ($user->isTutor() && !$user->wallet) {
            Wallet::create(['user_id' => $user->id, 'balance' => 0, 'currency' => 'KES']);
        }

        Auth::login($user);

        if ($user->isTutor() && !$user->tutorProfile) {
            return redirect()->route('tutor.onboarding');
        }

        if ($user->isAdmin())  return redirect()->route('admin.dashboard');
        if ($user->isTutor())  return redirect()->route('tutor.dashboard');
        return redirect()->route('student.dashboard');
    }
}
