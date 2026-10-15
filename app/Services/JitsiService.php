<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Firebase\JWT\JWT;

class JitsiService
{
    protected string $appId;
    protected string $appSecret;
    protected string $domain;

    public function __construct()
    {
        $this->appId     = config('jitsi.app_id', '');
        $this->appSecret = config('jitsi.app_secret', '');
        $this->domain    = config('jitsi.domain', 'meet.jit.si');
    }

    public function generateRoomName(Booking $booking): string
    {
        return 'tutorlink-session-' . $booking->uuid;
    }

    public function generateToken(User $user, string $room, bool $isModerator = false): ?string
    {
        if (empty($this->appId) || empty($this->appSecret)) {
            return null; // No JWT needed for public meet.jit.si
        }

        $now = time();
        $payload = [
            'iss'  => $this->appId,
            'sub'  => $this->domain,
            'aud'  => $this->appId,
            'exp'  => $now + 3600,
            'nbf'  => $now - 60,
            'room' => $room,
            'context' => [
                'user' => [
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'avatar'     => $user->avatar_url,
                    'moderator'  => $isModerator,
                ],
                'features' => [
                    'livestreaming' => false,
                    'recording'     => $isModerator,
                    'transcription' => false,
                    'outbound-call' => false,
                ],
            ],
        ];

        return JWT::encode($payload, $this->appSecret, 'HS256');
    }

    public function getMeetingUrl(Booking $booking, User $user): string
    {
        $room  = $this->generateRoomName($booking);
        $token = $this->generateToken($user, $room, $user->id === $booking->tutor_id);

        $url = "https://{$this->domain}/{$room}";
        if ($token) {
            $url .= "?jwt={$token}";
        }
        return $url;
    }
}
