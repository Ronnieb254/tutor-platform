<?php

namespace App\Services;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\Request;

class LoginActivityService
{
    /**
     * Record a successful login.
     */
    public function recordSuccess(User $user, Request $request): LoginActivity
    {
        return LoginActivity::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
            'reason' => null,
            'created_at' => now(),
        ]);
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailure(string $email, Request $request, string $reason): LoginActivity
    {
        return LoginActivity::create([
            'user_id' => null,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }

    /**
     * Check if the login is suspicious.
     * Returns true if:
     * - User agent hash not in last 10 successful logins, OR
     * - >= 5 failed login attempts for this email in last 30 minutes
     */
    public function checkSuspiciousActivity(User $user, Request $request): bool
    {
        $currentUserAgent = $request->userAgent();
        $currentUserAgentHash = md5($currentUserAgent);

        // Check if this is a new device (user agent not in last 10 successful logins)
        $recentSuccessfulLogins = LoginActivity::where('user_id', $user->id)
            ->where('status', 'success')
            ->latest('created_at')
            ->limit(10)
            ->get();

        $isNewDevice = true;
        foreach ($recentSuccessfulLogins as $activity) {
            if (md5($activity->user_agent) === $currentUserAgentHash) {
                $isNewDevice = false;
                break;
            }
        }

        if ($isNewDevice && $recentSuccessfulLogins->count() > 0) {
            return true;
        }

        // Check for >= 5 failed login attempts in last 30 minutes
        $failedAttemptsCount = LoginActivity::where('email', $user->email)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->count();

        if ($failedAttemptsCount >= 5) {
            return true;
        }

        return false;
    }
}
