<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BigBlueButtonService
{
    protected $serverUrl;
    protected $secret;

    public function __construct()
    {
        $this->serverUrl = rtrim(env('BBB_SERVER_URL', 'https://bbb.mwalimu.co.ke/bigbluebutton'), '/');
        $this->secret = env('BBB_SECRET', '');
    }

    /**
     * Generate checksum for API calls
     */
    protected function generateChecksum(string $method, array $params): string
    {
        $query = http_build_query($params);
        $data = $method . $query . $this->secret;
        return sha1($data);
    }

    /**
     * Create a BigBlueButton meeting
     * 
     * @param string $meetingID Unique meeting identifier
     * @param string $meetingName Display name for the meeting
     * @param string $attendeePW Password for attendees
     * @param string $moderatorPW Password for moderators
     * @param array $options Additional options
     * @return array Response from BBB API
     */
    public function createMeeting(
        string $meetingID,
        string $meetingName,
        string $attendeePW,
        string $moderatorPW,
        array $options = []
    ): array {
        $params = array_merge([
            'meetingID' => $meetingID,
            'name' => $meetingName,
            'attendeePW' => $attendeePW,
            'moderatorPW' => $moderatorPW,
            'welcome' => $options['welcome'] ?? 'Welcome to the session!',
            'record' => $options['record'] ?? 'true',
            'autoStartRecording' => $options['autoStartRecording'] ?? 'false',
            'allowStartStopRecording' => $options['allowStartStopRecording'] ?? 'true',
            'maxParticipants' => $options['maxParticipants'] ?? 50,
            'logoutURL' => $options['logoutURL'] ?? route('dashboard'),
            'lockSettingsDisableCam' => $options['lockSettingsDisableCam'] ?? 'false',
            'lockSettingsDisableMic' => $options['lockSettingsDisableMic'] ?? 'false',
            'guestPolicy' => $options['guestPolicy'] ?? 'ALWAYS_DENY', // Only authenticated users
        ], $options);

        $checksum = $this->generateChecksum('create', $params);
        $params['checksum'] = $checksum;

        $url = $this->serverUrl . '/api/create?' . http_build_query($params);
        
        try {
            $response = Http::timeout(10)->get($url);
            return $this->parseXmlResponse($response->body());
        } catch (\Exception $e) {
            return ['returncode' => 'FAILED', 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate join URL for a meeting
     * 
     * @param string $meetingID Meeting identifier
     * @param string $fullName Participant's name
     * @param string $password Meeting password (attendee or moderator)
     * @param bool $isModerator Whether user is a moderator
     * @param string|null $userID Optional user identifier
     * @return string Join URL
     */
    public function getJoinUrl(
        string $meetingID,
        string $fullName,
        string $password,
        bool $isModerator = false,
        ?string $userID = null
    ): string {
        $params = [
            'meetingID' => $meetingID,
            'fullName' => $fullName,
            'password' => $password,
        ];

        if ($userID) {
            $params['userID'] = $userID;
        }

        // Add custom parameters
        if ($isModerator) {
            $params['role'] = 'MODERATOR';
        }

        $checksum = $this->generateChecksum('join', $params);
        $params['checksum'] = $checksum;

        return $this->serverUrl . '/api/join?' . http_build_query($params);
    }

    /**
     * Check if a meeting is running
     * 
     * @param string $meetingID Meeting identifier
     * @return bool
     */
    public function isMeetingRunning(string $meetingID): bool
    {
        $params = ['meetingID' => $meetingID];
        $checksum = $this->generateChecksum('isMeetingRunning', $params);
        $params['checksum'] = $checksum;

        $url = $this->serverUrl . '/api/isMeetingRunning?' . http_build_query($params);
        
        try {
            $response = Http::timeout(5)->get($url);
            $result = $this->parseXmlResponse($response->body());
            return isset($result['running']) && $result['running'] === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get meeting info
     * 
     * @param string $meetingID Meeting identifier
     * @param string $moderatorPW Moderator password
     * @return array
     */
    public function getMeetingInfo(string $meetingID, string $moderatorPW): array
    {
        $params = [
            'meetingID' => $meetingID,
            'password' => $moderatorPW,
        ];
        
        $checksum = $this->generateChecksum('getMeetingInfo', $params);
        $params['checksum'] = $checksum;

        $url = $this->serverUrl . '/api/getMeetingInfo?' . http_build_query($params);
        
        try {
            $response = Http::timeout(5)->get($url);
            return $this->parseXmlResponse($response->body());
        } catch (\Exception $e) {
            return ['returncode' => 'FAILED', 'message' => $e->getMessage()];
        }
    }

    /**
     * End a meeting
     * 
     * @param string $meetingID Meeting identifier
     * @param string $moderatorPW Moderator password
     * @return array
     */
    public function endMeeting(string $meetingID, string $moderatorPW): array
    {
        $params = [
            'meetingID' => $meetingID,
            'password' => $moderatorPW,
        ];
        
        $checksum = $this->generateChecksum('end', $params);
        $params['checksum'] = $checksum;

        $url = $this->serverUrl . '/api/end?' . http_build_query($params);
        
        try {
            $response = Http::timeout(5)->get($url);
            return $this->parseXmlResponse($response->body());
        } catch (\Exception $e) {
            return ['returncode' => 'FAILED', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get recordings for a meeting
     * 
     * @param string $meetingID Meeting identifier
     * @return array
     */
    public function getRecordings(string $meetingID): array
    {
        $params = ['meetingID' => $meetingID];
        $checksum = $this->generateChecksum('getRecordings', $params);
        $params['checksum'] = $checksum;

        $url = $this->serverUrl . '/api/getRecordings?' . http_build_query($params);
        
        try {
            $response = Http::timeout(10)->get($url);
            return $this->parseXmlResponse($response->body());
        } catch (\Exception $e) {
            return ['returncode' => 'FAILED', 'message' => $e->getMessage()];
        }
    }

    /**
     * Parse XML response from BBB API
     */
    protected function parseXmlResponse(string $xml): array
    {
        try {
            $xmlObj = simplexml_load_string($xml);
            return json_decode(json_encode($xmlObj), true);
        } catch (\Exception $e) {
            return ['returncode' => 'FAILED', 'message' => 'Failed to parse XML response'];
        }
    }

    /**
     * Generate random password
     */
    public function generatePassword(int $length = 12): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
