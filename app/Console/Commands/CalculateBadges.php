<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PublicProfile\BadgeCalculationService;

class CalculateBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badges:calculate {--account= : Calculate for specific account ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and award verification badges for trading accounts';

    /**
     * Execute the console command.
     */
    public function handle(BadgeCalculationService $badgeService)
    {
        $this->info('Starting badge calculation...');

        if ($accountId = $this->option('account')) {
            // Calculate for specific account
            $account = \App\Models\TradingAccount::findOrFail($accountId);
            $badges = $badgeService->calculateForAccount($account);
            
            $this->info("Calculated badges for account #{$accountId}");
            $this->info("Badges awarded: " . count($badges));
            
            foreach ($badges as $badge) {
                $this->line("  - {$badge->badge_name} ({$badge->badge_icon})");
            }
        } else {
            // Calculate for all accounts
            $results = $badgeService->calculateForAllAccounts();
            
            $this->info("Badge calculation complete!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Accounts Processed', $results['processed']],
                    ['Badges Awarded', $results['badges_awarded']],
                ]
            );
        }

        $this->info('✓ Done!');
        return Command::SUCCESS;
    }
}
