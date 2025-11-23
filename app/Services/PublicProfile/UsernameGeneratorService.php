<?php

namespace App\Services\PublicProfile;

class UsernameGeneratorService
{
    private UsernameValidationService $validator;

    public function __construct(UsernameValidationService $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Generate available username from desired username
     * If taken, append _{random-001-999}
     */
    public function generate(string $desiredUsername): string
    {
        // Clean the username
        $username = $this->clean($desiredUsername);

        // If available, return as is
        if ($this->validator->isAvailable($username)) {
            return $username;
        }

        // Try up to 50 attempts to find available username
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $randomNumber = rand(1, 999);
            $newUsername = $username . '_' . str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
            
            if ($this->validator->isAvailable($newUsername)) {
                return $newUsername;
            }
        }

        // Fallback: append timestamp
        return $username . '_' . substr(time(), -6);
    }

    /**
     * Clean username (remove special chars, lowercase)
     */
    private function clean(string $username): string
    {
        // Convert to lowercase
        $username = strtolower($username);
        
        // Remove all except letters, numbers, underscores
        $username = preg_replace('/[^a-z0-9_]/', '', $username);
        
        // Trim to max length
        $username = substr($username, 0, 50);
        
        return $username;
    }
}
