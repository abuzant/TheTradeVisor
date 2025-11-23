<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\PublicProfile\UsernameValidationService;
use App\Services\PublicProfile\UsernameGeneratorService;
use App\Services\PublicProfile\ProfileDataAggregatorService;
use App\Models\PublicProfileAccount;
use App\Models\User;

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

    /**
     * Show account public profiles management page
     */
    public function manageAccounts(Request $request)
    {
        $accounts = $request->user()
            ->tradingAccounts()
            ->with('publicProfileAccount')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('accounts.public-profiles', compact('accounts'));
    }

    /**
     * Update account public profile settings
     */
    public function updateAccountProfile(Request $request, $accountId)
    {
        $account = $request->user()
            ->tradingAccounts()
            ->findOrFail($accountId);

        $validated = $request->validate([
            'is_public' => 'required|boolean',
            'account_slug' => 'required|string|min:3|max:100|regex:/^[a-z0-9-]+$/',
            'custom_title' => 'nullable|string|max:150',
            'widget_preset' => 'required|in:minimal,full_stats,trader_showcase,custom',
            'show_symbols' => 'required|boolean',
            'show_recent_trades' => 'required|boolean',
        ]);

        // Check if slug is unique for this user
        $existingSlug = \App\Models\PublicProfileAccount::where('user_id', $request->user()->id)
            ->where('account_slug', $validated['account_slug'])
            ->where('trading_account_id', '!=', $accountId)
            ->exists();

        if ($existingSlug) {
            return response()->json(['message' => 'This slug is already used by another account'], 422);
        }

        // Create or update public profile account
        $profileAccount = $account->publicProfileAccount()->updateOrCreate(
            ['trading_account_id' => $accountId],
            [
                'user_id' => $request->user()->id,
                'account_slug' => $validated['account_slug'],
                'is_public' => $validated['is_public'],
                'custom_title' => $validated['custom_title'],
                'widget_preset' => $validated['widget_preset'],
                'show_symbols' => $validated['show_symbols'],
                'show_recent_trades' => $validated['show_recent_trades'],
            ]
        );

        return response()->json(['success' => true, 'profile' => $profileAccount]);
    }

    /**
     * Show public profile
     * URL: /@{username}/{slug}/{account_number}
     */
    public function show(Request $request, string $username, string $slug, string $accountNumber, ProfileDataAggregatorService $dataAggregator)
    {
        // Handle @anonymous special case
        if ($username === 'anonymous') {
            $user = User::whereHas('publicProfileAccounts', function ($query) use ($slug, $accountNumber) {
                $query->where('account_slug', $slug)
                      ->whereHas('tradingAccount', function ($q) use ($accountNumber) {
                          $q->where('account_number', $accountNumber);
                      });
            })->where('public_display_mode', 'anonymous')->first();
        } else {
            $user = User::where('public_username', $username)->first();
        }

        if (!$user) {
            abort(404, 'Profile not found');
        }

        // Find the public profile account
        $profileAccount = PublicProfileAccount::where('user_id', $user->id)
            ->where('account_slug', $slug)
            ->where('is_public', true)
            ->whereHas('tradingAccount', function ($query) use ($accountNumber) {
                $query->where('account_number', $accountNumber);
            })
            ->with(['tradingAccount', 'user'])
            ->first();

        if (!$profileAccount) {
            abort(404, 'Profile not found or not public');
        }

        // Get all profile data
        $data = $dataAggregator->getProfileData($profileAccount);

        // Track view (async, don't block)
        $this->trackView($request, $profileAccount);

        return view('public-profile.show', $data);
    }

    /**
     * Track profile view
     */
    private function trackView(Request $request, PublicProfileAccount $profileAccount): void
    {
        try {
            \App\Models\ProfileView::create([
                'public_profile_account_id' => $profileAccount->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
                'viewed_at' => now(),
            ]);

            $profileAccount->incrementViewCount();
        } catch (\Exception $e) {
            // Silently fail, don't break profile view
            \Log::error('Failed to track profile view: ' . $e->getMessage());
        }
    }
}
