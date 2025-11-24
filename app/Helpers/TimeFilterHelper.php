<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeFilterHelper
{
    /**
     * Get available time periods for standard users (7 days max)
     */
    public static function getStandardPeriods(): array
    {
        return [
            'today' => [
                'label' => 'Today',
                'days' => 0,
                'locked' => false,
            ],
            '7d' => [
                'label' => '7 Days',
                'days' => 7,
                'locked' => false,
            ],
            '30d' => [
                'label' => '30 Days',
                'days' => 30,
                'locked' => true,
            ],
            '90d' => [
                'label' => '90 Days',
                'days' => 90,
                'locked' => true,
            ],
            '180d' => [
                'label' => '180 Days',
                'days' => 180,
                'locked' => true,
            ],
        ];
    }

    /**
     * Get available time periods for enterprise users (180 days max)
     */
    public static function getEnterprisePeriods(): array
    {
        return [
            'today' => [
                'label' => 'Today',
                'days' => 0,
                'locked' => false,
            ],
            '7d' => [
                'label' => '7 Days',
                'days' => 7,
                'locked' => false,
            ],
            '30d' => [
                'label' => '30 Days',
                'days' => 30,
                'locked' => false,
            ],
            '90d' => [
                'label' => '90 Days',
                'days' => 90,
                'locked' => false,
            ],
            '180d' => [
                'label' => '180 Days',
                'days' => 180,
                'locked' => false,
            ],
        ];
    }

    /**
     * Get periods based on account's max days view
     */
    public static function getPeriodsForAccount($account): array
    {
        // Handle guest/demo access (null account)
        if (!$account) {
            $periods = self::getStandardPeriods();
            // Unlock 30 days for public demo
            if (isset($periods['30d'])) {
                $periods['30d']['locked'] = false;
            }
            return $periods;
        }

        $maxDays = $account->getMaxDaysView();
        
        if ($maxDays >= 180) {
            return self::getEnterprisePeriods();
        }
        
        return self::getStandardPeriods();
    }

    /**
     * Get periods based on user's data access (checks all accounts)
     */
    public static function getPeriodsForUser($user): array
    {
        // Get the maximum data access days from all user's accounts
        $maxDays = $user->tradingAccounts()
            ->get()
            ->map(fn($account) => $account->getMaxDaysView())
            ->max() ?? 7; // Default to 7 if no accounts
        
        if ($maxDays >= 180) {
            return self::getEnterprisePeriods();
        }
        
        return self::getStandardPeriods();
    }

    /**
     * Get date range for a period
     */
    public static function getDateRange(string $period): array
    {
        $days = match($period) {
            'today' => 0,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '180d' => 180,
            default => 7,
        };

        if ($days === 0) {
            return [
                'start' => Carbon::today(),
                'end' => Carbon::now(),
                'days' => 0,
            ];
        }

        return [
            'start' => Carbon::now()->subDays($days),
            'end' => Carbon::now(),
            'days' => $days,
        ];
    }

    /**
     * Check if period is locked for account
     */
    public static function isPeriodLocked($account, string $period): bool
    {
        $maxDays = $account->getMaxDaysView();
        $periodDays = self::getDateRange($period)['days'];
        
        return $periodDays > $maxDays;
    }

    /**
     * Get default period for account
     */
    public static function getDefaultPeriod($account): string
    {
        return '7d'; // Default to 7 days for all users
    }
}
