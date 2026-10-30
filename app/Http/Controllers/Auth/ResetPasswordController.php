<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetSuccessNotification;
use App\Services\PasswordHistoryService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password};
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class ResetPasswordController extends Controller
{
    public function __construct(
        protected PasswordHistoryService $passwordHistory
    ) {}

    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle an incoming password reset request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                // Check if password is being reused
                if ($this->passwordHistory->checkPasswordReuse($user, $password)) {
                    throw new \Exception('You cannot reuse any of your last 3 passwords.');
                }

                // Update the password
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Store new password in history
                $this->passwordHistory->storePassword($user);

                // Send notification
                $user->notify(new PasswordResetSuccessNotification(
                    $request->ip(),
                    $request->userAgent()
                ));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Your password has been reset successfully. Please log in with your new password.');
        }

        // Check for our custom exception message
        $errorBag = $request->session()->get('errors');
        if ($errorBag && $errorBag->has('email')) {
            $errors = $errorBag->get('email');
            if (in_array('You cannot reuse any of your last 3 passwords.', $errors)) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'You cannot reuse any of your last 3 passwords.']);
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
