<?php

namespace App\Services\PublicProfile;

use App\Models\TradingAccount;
use App\Models\ProfileVerificationBadge;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;

class BadgeCalculationService
{
    /**
     * Badge definitions with rules
     */
    private const BADGES = [
        // Time-Based Badges (Account Age)
        'new_trader' => [
            'name' => 'New Trader',
            'icon' => '🌱',
            'color' => 'green',
            'tier' => 1,
            'condition' => 'account_age_days < 30',
        ],
        'verified_trader' => [
            'name' => 'Verified Trader',
            'icon' => '<i class="fas fa-check-circle"></i>',
            'color' => 'blue',
            'tier' => 2,
            'condition' => 'account_age_days >= 30',
        ],
        'veteran_3m' => [
            'name' => '3-Month Veteran',
            'icon' => '⭐',
            'color' => 'yellow',
            'tier' => 3,
            'condition' => 'account_age_days >= 90',
        ],
        'veteran_6m' => [
            'name' => '6-Month Veteran',
            'icon' => '⭐⭐',
            'color' => 'yellow',
            'tier' => 4,
            'condition' => 'account_age_days >= 180',
        ],
        'veteran_1y' => [
            'name' => 'Yearly Veteran',
            'icon' => '🏆',
            'color' => 'purple',
            'tier' => 5,
            'condition' => 'account_age_days >= 365',
        ],
        'long_term_trader' => [
            'name' => 'Long-Term Trader',
            'icon' => '💎',
            'color' => 'indigo',
            'tier' => 6,
            'condition' => 'account_age_days >= 730',
        ],

        // Activity-Based Badges (Trade Count)
        'active_trader' => [
            'name' => 'Active Trader',
            'icon' => '📊',
            'color' => 'blue',
            'tier' => 2,
            'condition' => 'closed_trades >= 50',
        ],
        'experienced_trader' => [
            'name' => 'Experienced Trader',
            'icon' => '📈',
            'color' => 'green',
            'tier' => 3,
            'condition' => 'closed_trades >= 100',
        ],
        'seasoned_trader' => [
            'name' => 'Seasoned Trader',
            'icon' => '🎯',
            'color' => 'yellow',
            'tier' => 4,
            'condition' => 'closed_trades >= 500',
        ],
        'professional_trader' => [
            'name' => 'Professional Trader',
            'icon' => '🚀',
            'color' => 'purple',
            'tier' => 5,
            'condition' => 'closed_trades >= 1000',
        ],
        'elite_trader' => [
            'name' => 'Elite Trader',
            'icon' => '💼',
            'color' => 'indigo',
            'tier' => 6,
            'condition' => 'closed_trades >= 5000',
        ],

        // Performance-Based Badges
        'profitable_trader' => [
            'name' => 'Profitable Trader',
            'icon' => '🔥',
            'color' => 'green',
            'tier' => 2,
            'condition' => 'total_profit > 0',
        ],

        // Enterprise Badges
        'enterprise_account' => [
            'name' => 'Enterprise Account',
            'icon' => '🏢',
            'color' => 'blue',
            'tier' => 3,
            'condition' => 'is_enterprise_whitelisted',
        ],
        'premium_access' => [
            'name' => 'Premium Access',
            'icon' => '⚡',
            'color' => 'purple',
            'tier' => 3,
            'condition' => 'has_180_day_access',
        ],
    ];

    /**
     * Calculate and award badges for a trading account
     */
    public function calculateForAccount(TradingAccount $account): array
    {
        $stats = $this->getAccountStats($account);
        $awardedBadges = [];

        foreach (self::BADGES as $badgeType => $badgeConfig) {
            if ($this->shouldAwardBadge($badgeType, $badgeConfig, $stats, $account)) {
                $badge = $this->awardBadge($account, $badgeType, $badgeConfig);
                $awardedBadges[] = $badge;
            } else {
                // Remove badge if no longer qualifies
                $this->removeBadge($account, $badgeType);
            }
        }

        return $awardedBadges;
    }

    /**
     * Calculate badges for all accounts
     */
    public function calculateForAllAccounts(): array
    {
        $accounts = TradingAccount::where('is_active', true)->get();
        $results = [
            'processed' => 0,
            'badges_awarded' => 0,
            'badges_removed' => 0,
        ];

        foreach ($accounts as $account) {
            $badges = $this->calculateForAccount($account);
            $results['processed']++;
            $results['badges_awarded'] += count($badges);
        }

        return $results;
    }

    /**
     * Get account statistics for badge calculation
     */
    private function getAccountStats(TradingAccount $account): array
    {
        // Account age in days
        $firstTrade = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'in')
            ->orderBy('time', 'asc')
            ->first();

        $accountAgeDate = $firstTrade ? $firstTrade->time : $account->created_at;
        $accountAgeDays = now()->diffInDays($accountAgeDate);

        // Closed trades count (MT5: deals with entry='out')
        $closedTrades = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->count();

        // Total profit (sum of all out deals)
        $totalProfit = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->sum('profit');

        // Enterprise status
        $isEnterpriseWhitelisted = $account->isEnterpriseWhitelisted();
        $has180DayAccess = $account->getMaxDaysView() === 180;

        return [
            'account_age_days' => $accountAgeDays,
            'closed_trades' => $closedTrades,
            'total_profit' => $totalProfit,
            'is_enterprise_whitelisted' => $isEnterpriseWhitelisted,
            'has_180_day_access' => $has180DayAccess,
        ];
    }

    /**
     * Check if badge should be awarded
     */
    private function shouldAwardBadge(string $badgeType, array $badgeConfig, array $stats, TradingAccount $account): bool
    {
        $condition = $badgeConfig['condition'];

        // Parse condition and evaluate
        if (str_contains($condition, 'account_age_days')) {
            preg_match('/account_age_days ([<>=]+) (\d+)/', $condition, $matches);
            if ($matches) {
                $operator = $matches[1];
                $value = (int)$matches[2];
                return $this->evaluateCondition($stats['account_age_days'], $operator, $value);
            }
        }

        if (str_contains($condition, 'closed_trades')) {
            preg_match('/closed_trades ([<>=]+) (\d+)/', $condition, $matches);
            if ($matches) {
                $operator = $matches[1];
                $value = (int)$matches[2];
                return $this->evaluateCondition($stats['closed_trades'], $operator, $value);
            }
        }

        if (str_contains($condition, 'total_profit')) {
            preg_match('/total_profit ([<>=]+) (\d+)/', $condition, $matches);
            if ($matches) {
                $operator = $matches[1];
                $value = (int)$matches[2];
                return $this->evaluateCondition($stats['total_profit'], $operator, $value);
            }
        }

        if ($condition === 'is_enterprise_whitelisted') {
            return $stats['is_enterprise_whitelisted'];
        }

        if ($condition === 'has_180_day_access') {
            return $stats['has_180_day_access'];
        }

        return false;
    }

    /**
     * Evaluate condition
     */
    private function evaluateCondition($value, string $operator, $compareValue): bool
    {
        return match($operator) {
            '<' => $value < $compareValue,
            '<=' => $value <= $compareValue,
            '>' => $value > $compareValue,
            '>=' => $value >= $compareValue,
            '==' => $value == $compareValue,
            '!=' => $value != $compareValue,
            default => false,
        };
    }

    /**
     * Award badge to account
     */
    private function awardBadge(TradingAccount $account, string $badgeType, array $badgeConfig): ProfileVerificationBadge
    {
        return ProfileVerificationBadge::updateOrCreate(
            [
                'trading_account_id' => $account->id,
                'badge_type' => $badgeType,
            ],
            [
                'badge_name' => $badgeConfig['name'],
                'badge_icon' => $badgeConfig['icon'],
                'badge_color' => $badgeConfig['color'],
                'badge_tier' => $badgeConfig['tier'],
                'earned_at' => now(),
            ]
        );
    }

    /**
     * Remove badge from account
     */
    private function removeBadge(TradingAccount $account, string $badgeType): void
    {
        ProfileVerificationBadge::where('trading_account_id', $account->id)
            ->where('badge_type', $badgeType)
            ->delete();
    }

    /**
     * Get badges for display (3 highest + 2 recent + 1 favorite)
     */
    public function getBadgesForDisplay(TradingAccount $account): array
    {
        $allBadges = $account->verificationBadges;

        // Get favorite badge
        $favorite = $allBadges->where('is_favorite', true)->first();

        // Get 3 highest tier badges
        $highestTier = $allBadges->sortByDesc('badge_tier')->take(3);

        // Get 2 most recent badges
        $mostRecent = $allBadges->sortByDesc('earned_at')->take(2);

        // Combine and deduplicate
        $displayBadges = collect()
            ->merge($favorite ? [$favorite] : [])
            ->merge($highestTier)
            ->merge($mostRecent)
            ->unique('id')
            ->take(6)
            ->values();

        return $displayBadges->toArray();
    }
}
