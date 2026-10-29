<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PasswordSecurityService
{
    /**
     * Check if a password has been found in a data breach using Have I Been Pwned API.
     * Uses k-anonymity model: only sends first 5 chars of SHA-1 hash.
     * 
     * @param string $password The password to check
     * @return bool True if password was found in a breach, false if safe
     */
    public function checkHibpBreach(string $password): bool
    {
        try {
            // Hash the password with SHA-1
            $hash = strtoupper(sha1($password));
            
            // Split into prefix (first 5 chars) and suffix (remaining chars)
            $prefix = substr($hash, 0, 5);
            $suffix = substr($hash, 5);
            
            // Query HIBP API with just the prefix (k-anonymity)
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mwalimu-Tutor-Platform',
                    'Add-Padding' => 'true', // Request padding for additional privacy
                ])
                ->get("https://api.pwnedpasswords.com/range/{$prefix}");
            
            if (!$response->successful()) {
                // If API is down, log but don't block registration
                Log::warning('HIBP API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false; // Fail open: don't block if API is unreachable
            }
            
            // Parse response: each line is "SUFFIX:COUNT"
            $hashes = explode("\r\n", $response->body());
            
            foreach ($hashes as $line) {
                if (empty($line)) {
                    continue;
                }
                
                [$hashSuffix, $count] = explode(':', $line);
                
                // Check if our password's hash suffix matches
                if (strtoupper($hashSuffix) === $suffix) {
                    Log::info('Password found in HIBP database', [
                        'breach_count' => $count,
                        'hash_prefix' => $prefix,
                    ]);
                    return true; // Password is compromised
                }
            }
            
            // Password not found in breaches
            return false;
            
        } catch (\Exception $e) {
            // Log error but don't block registration if API fails
            Log::error('HIBP breach check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false; // Fail open
        }
    }
}

