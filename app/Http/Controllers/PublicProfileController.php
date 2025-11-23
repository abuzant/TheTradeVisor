<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\PublicProfile\UsernameValidationService;
use App\Services\PublicProfile\UsernameGeneratorService;

class PublicProfileController extends Controller
{
    public function __construct(
        private UsernameValidationService $usernameValidator,
        private UsernameGeneratorService $usernameGenerator
    ) {}

    /**
     * Update user's public profile settings
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();

        // Validate request
        $validated = $request->validate([
            'public_username' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                function ($attribute, $value, $fail) use ($user) {
                    // Only validate if username is being set for first time
                    if ($user->public_username) {
                        return; // Already set, skip validation
                    }

                    if ($value) {
                        // Check if reserved
                        if ($this->usernameValidator->isReserved($value)) {
                            $fail('This username is reserved and cannot be used.');
                        }

                        // Check if contains profanity
                        if ($this->usernameValidator->containsProfanity($value)) {
                            $fail('This username contains inappropriate content.');
                        }

                        // Check if available
                        if (!$this->usernameValidator->isAvailable($value)) {
                            // Auto-generate alternative
                            $alternative = $this->usernameGenerator->generate($value);
                            $fail("Username '{$value}' is taken. Try: {$alternative}");
                        }
                    }
                },
            ],
            'public_display_mode' => 'required|in:username,anonymous,custom_name',
            'public_display_name' => 'nullable|string|max:100',
            'show_on_leaderboard' => 'nullable|boolean',
            'leaderboard_rank_by' => 'required|in:total_profit,roi,win_rate,profit_factor',
        ]);

        // Handle username setting (one-time only)
        if (!$user->public_username && $request->filled('public_username')) {
            $user->public_username = $validated['public_username'];
            $user->public_username_set_at = now();
        }

        // Update other settings
        $user->public_display_mode = $validated['public_display_mode'];
        $user->public_display_name = $validated['public_display_name'] ?? null;
        $user->show_on_leaderboard = $request->boolean('show_on_leaderboard');
        $user->leaderboard_rank_by = $validated['leaderboard_rank_by'];

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
