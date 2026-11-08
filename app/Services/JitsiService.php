<?php

namespace App\Services;

use Firebase\JWT\JWT;

class JitsiService
{
    protected $domain;
    protected $appId;
    protected $appSecret;
    protected $useJWT;

    public function __construct()
    {
        $this->domain = env('JITSI_DOMAIN', 'meet.jit.si');
        $this->appId = env('JITSI_APP_ID', '');
        $this->appSecret = env('JITSI_APP_SECRET', '');
        $this->useJWT = env('JITSI_USE_JWT', false);

        if (str_starts_with($this->appId, 'vpaas-magic-cookie')) {
            $this->domain = '8x8.vc';
        }
    }

    /**
     * Create a Jitsi meeting URL
     * 
     * @param string $roomName Unique room identifier
     * @param string $displayName Participant's display name
     * @param bool $isModerator Whether user is a moderator
     * @param array $options Additional options
     * @return string Meeting URL
     */
    public function createMeeting(
        string $roomName,
        string $displayName,
        bool $isModerator = false,
        array $options = []
    ): string {
        // Sanitize room name (remove spaces, special characters)
        $roomName = $this->sanitizeRoomName($roomName);

        // Build base URL
        $baseUrl = "https://{$this->domain}/{$roomName}";

        // If JWT is enabled, generate token
        if ($this->useJWT && $this->appId && $this->appSecret) {
            $jwt = $this->generateJWT($roomName, $displayName, $isModerator, $options);
            return $baseUrl . "?jwt=" . $jwt;
        }

        // Otherwise, use URL parameters
        $params = [
            'displayName' => $displayName,
        ];

        // Add custom parameters
        if (isset($options['email'])) {
            $params['email'] = $options['email'];
        }

        if (isset($options['avatarUrl'])) {
            $params['avatarUrl'] = $options['avatarUrl'];
        }

        // Add config overrides
        $config = [];
        
        if ($isModerator) {
            $config['startWithAudioMuted'] = false;
            $config['startWithVideoMuted'] = false;
        }

        // Disable features for non-moderators if needed
        if (!$isModerator && isset($options['restrictGuests']) && $options['restrictGuests']) {
            $config['disableInviteFunctions'] = true;
        }

        // Recording options
        if (isset($options['recording'])) {
            $config['startRecordingOnJoin'] = $options['recording'];
        }

        if (!empty($config)) {
            $params['config'] = json_encode($config);
        }

        return $baseUrl . '#config.' . http_build_query($params);
    }

    /**
     * Generate JWT token for authenticated Jitsi meetings.
     * Uses RS256 + JaaS claims for vpaas-magic-cookie accounts.
     */
    protected function generateJWT(
        string $roomName,
        string $displayName,
        bool $isModerator,
        array $options = []
    ): string {
        $now    = time();
        $keyId  = $this->appId;  // for JaaS the kid IS the app_id (vpaas-magic-cookie-...)
        $payload = [
            'aud'     => 'jitsi',
            'iss'     => $this->appId,
            'sub'     => '8x8.vc',
            'room'    => '*',          // wildcard allows any room under this app
            'exp'     => $now + 7200,
            'nbf'     => $now - 10,
            'context' => [
                'user' => [
                    'name'      => $displayName,
                    'email'     => $options['email'] ?? '',
                    'moderator' => $isModerator,
                ],
            ],
        ];
        return \Firebase\JWT\JWT::encode($payload, $this->appSecret, 'RS256', $keyId);
    }

    /**
     * Sanitize room name for Jitsi
     * Removes spaces, special characters, and converts to safe format
     */
    protected function sanitizeRoomName(string $roomName): string
    {
        // Remove special characters, keep alphanumeric and hyphens
        $roomName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $roomName);
        
        // Ensure it starts with a letter (Jitsi requirement)
        if (!preg_match('/^[a-zA-Z]/', $roomName)) {
            $roomName = 'room-' . $roomName;
        }

        return $roomName;
    }

    /**
     * Get meeting URL (alias for createMeeting for backward compatibility)
     */
    public function getJoinUrl(
        string $roomName,
        string $displayName,
        bool $isModerator = false,
        array $options = []
    ): string {
        return $this->createMeeting($roomName, $displayName, $isModerator, $options);
    }

    /**
     * Generate embed URL for iframe integration
     */
    public function getEmbedUrl(
        string $roomName,
        string $displayName,
        bool $isModerator = false,
        array $options = []
    ): string {
        $meetingUrl = $this->createMeeting($roomName, $displayName, $isModerator, $options);
        
        // Add iframe-specific parameters
        $embedParams = [
            'interfaceConfigOverwrite' => json_encode([
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
                'TOOLBAR_BUTTONS' => [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 
                    'fullscreen', 'fodeviceselection', 'hangup', 'profile',
                    'chat', 'recording', 'livestreaming', 'etherpad', 'sharedvideo',
                    'settings', 'raisehand', 'videoquality', 'filmstrip',
                    'invite', 'feedback', 'stats', 'shortcuts', 'tileview',
                    'videobackgroundblur', 'download', 'help', 'mute-everyone',
                ],
            ]),
        ];

        return $meetingUrl . '&' . http_build_query($embedParams);
    }

    /**
     * Expose JWT generation for controller use.
     */
    public function makeJWT(
        string $roomName,
        string $displayName,
        bool $isModerator,
        array $options = []
    ): ?string {
        if (!$this->useJWT || !$this->appId || !$this->appSecret) {
            return null;
        }

        return $this->generateJWT(
            $this->sanitizeRoomName($roomName),
            $displayName,
            $isModerator,
            $options
        );
    }

    /**
     * Generate a sanitized room name from a Booking model.
     */
    public function generateRoomName(\App\Models\Booking $booking): string
    {
        return $this->sanitizeRoomName($booking->meeting_room ?? 'Mwalimu-session-' . $booking->uuid);
    }

    /**
     * Generate a Jitsi JWT token for a given user and room.
     */
    public function generateToken(\App\Models\User $user, string $roomName, bool $isModerator): ?string
    {
        return $this->makeJWT($roomName, $user->name, $isModerator, ['email' => $user->email]);
    }

    /**
     * Check if Jitsi is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->domain);
    }
}
