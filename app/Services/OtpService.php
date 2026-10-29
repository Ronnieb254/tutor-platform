<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and send an OTP for the given identifier (email or phone).
     * Returns the OtpVerification record.
     */
    public function send(string $identifier, string $type = 'email', string $name = ''): OtpVerification
    {
        // Invalidate any previous OTPs for this identifier
        OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('verified', false)
            ->delete();

        $otp = (string) random_int(100000, 999999);

        $record = OtpVerification::create([
            'identifier' => $identifier,
            'type'       => $type,
            'otp'        => $otp,
            'verified'   => false,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        if ($type === 'email') {
            $this->sendEmailOtp($identifier, $otp, $name);
        } else {
            $this->sendSmsOtp($identifier, $otp, $name);
        }

        return $record;
    }

    /**
     * Verify an OTP for the given identifier.
     * Returns true on success, or a string error message on failure.
     */
    public function verify(string $identifier, string $type, string $otp): bool|string
    {
        $record = OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$record) {
            return 'No OTP found. Please request a new one.';
        }

        if ($record->isExpired()) {
            $record->delete();
            return 'Your OTP has expired. Please request a new one.';
        }

        if ($record->isMaxAttemptsReached()) {
            $record->delete();
            return 'Too many failed attempts. Please request a new OTP.';
        }

        // Increment attempts before checking
        $record->increment('attempts');

        if ($record->otp !== $otp) {
            $remaining = 5 - $record->fresh()->attempts;
            return "Incorrect OTP. {$remaining} attempt(s) remaining.";
        }

        $record->update(['verified' => true]);

        return true;
    }

    /**
     * Check if the given identifier has a verified OTP.
     */
    public function isVerified(string $identifier, string $type): bool
    {
        return OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('verified', true)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Send OTP via email using Laravel Mail.
     */
    private function sendEmailOtp(string $email, string $otp, string $name): void
    {
        try {
            Mail::send([], [], function ($message) use ($email, $otp, $name) {
                $appName = config('app.name', 'Mwalimu');
                $message->to($email, $name)
                    ->subject("{$otp} is your {$appName} verification code")
                    ->html($this->emailTemplate($otp, $name, $appName));
            });

            Log::info("OTP email sent to {$email}");
        } catch (\Exception $e) {
            // Always log the OTP so it can be retrieved during debugging
            Log::error("OTP email FAILED for {$email} — OTP was: {$otp}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Send OTP via SMS (Africa's Talking or similar).
     * Reads SMS_PROVIDER from .env. Falls back to logging if not configured.
     */
    private function sendSmsOtp(string $phone, string $otp, string $name): void
    {
        $provider = config('services.sms.provider', 'log');

        try {
            if ($provider === 'africastalking') {
                $this->sendViaAfricasTalking($phone, $otp);
            } else {
                // Fallback: log the OTP (useful for development)
                Log::info("SMS OTP for {$phone}: {$otp}");
            }
        } catch (\Exception $e) {
            Log::error('OTP SMS failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Africa's Talking SMS integration.
     */
    private function sendViaAfricasTalking(string $phone, string $otp): void
    {
        $apiKey   = config('services.africastalking.api_key');
        $username = config('services.africastalking.username');
        $senderId = config('services.africastalking.sender_id', 'MWALIMU');

        $message = "Your Mwalimu verification code is: {$otp}. Valid for 10 minutes. Do not share.";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'apiKey' => $apiKey,
            'Accept' => 'application/json',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to'       => $phone,
            'message'  => $message,
            'from'     => $senderId,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Africa\'s Talking SMS failed: ' . $response->body());
        }
    }

    /**
     * HTML email template for the OTP.
     */
    private function emailTemplate(string $otp, string $name, string $appName): string
    {
        $firstName = explode(' ', trim($name))[0] ?? 'there';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background:#F2F5F2;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F2F5F2;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="520" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid rgba(21,40,31,.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#111C16,#15281F);padding:32px 40px;text-align:center;">
            <div style="font-family:Georgia,serif;font-size:28px;font-weight:700;color:#fff;letter-spacing:-0.5px;">{$appName}</div>
            <div style="font-size:13px;color:rgba(255,255,255,.45);margin-top:4px;">Secure Verification</div>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:40px;">
            <p style="font-size:18px;font-weight:600;color:#15281F;margin:0 0 8px;">Hi {$firstName} 👋</p>
            <p style="font-size:15px;color:rgba(21,40,31,.65);margin:0 0 32px;line-height:1.6;">
              Use the code below to verify your email address and complete your {$appName} registration.
            </p>
            <!-- OTP Box -->
            <div style="background:#F9FAF9;border-radius:16px;padding:28px;text-align:center;margin-bottom:32px;border:2px dashed rgba(21,40,31,.1);">
              <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.45);margin-bottom:12px;">Your verification code</div>
              <div style="font-family:'Courier New',monospace;font-size:42px;font-weight:800;color:#0A64BC;letter-spacing:12px;">{$otp}</div>
              <div style="font-size:13px;color:rgba(21,40,31,.45);margin-top:12px;">⏱ Expires in <strong>10 minutes</strong></div>
            </div>
            <p style="font-size:13px;color:rgba(21,40,31,.55);line-height:1.7;margin:0;">
              If you didn't create a {$appName} account, you can safely ignore this email. Do not share this code with anyone.
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#F9FAF9;padding:20px 40px;border-top:1px solid rgba(21,40,31,.06);">
            <p style="font-size:12px;color:rgba(21,40,31,.35);margin:0;text-align:center;">
              © {$appName} · Empowering learners nationwide
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
    }
}
