<?php

namespace App\Services;

use App\Models\{User, PasswordHistory};
use Illuminate\Support\Facades\Hash;

class PasswordHistoryService
{
    /**
     * Check if the new password matches any of the user's last 3 passwords.
     * Returns true if a match is found (password is being reused).
     */
    public function checkPasswordReuse(User $user, string $newPassword): bool
    {
        $recentPasswords = $user->passwordHistory()
            ->latest('created_at')
            ->limit(3)
            ->get();

        foreach ($recentPasswords as $history) {
            if (Hash::check($newPassword, $history->password)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store the user's current password hash in the password history table.
     */
    public function storePassword(User $user): void
    {
        PasswordHistory::create([
            'user_id'  => $user->id,
            'password' => $user->password,
        ]);
    }
}
