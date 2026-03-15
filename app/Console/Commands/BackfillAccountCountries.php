<?php

namespace App\Console\Commands;

use App\Models\TradingAccount;
use App\Services\GeoIPService;
use Illuminate\Console\Command;

class BackfillAccountCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:backfill-countries {--force : Force update even if country data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill country data for trading accounts using their IP addresses';

    protected GeoIPService $geoService;

    /**
     * Create a new command instance.
     */
    public function __construct(GeoIPService $geoService)
    {
        parent::__construct();
        $this->geoService = $geoService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->geoService->isDatabaseAvailable()) {
            $this->error('GeoIP database not found at: ' . $this->geoService->getDatabasePath());
            $this->info('Please run: php artisan geoip:update');
            return 1;
        }

        $this->info('Starting country data backfill...');
        $this->newLine();

        // Build query
        $query = TradingAccount::whereNotNull('last_seen_ip');
        
        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->whereNull('country_code')
                  ->orWhere('country_code', '');
            });
        }

        $accounts = $query->get();
        
        if ($accounts->isEmpty()) {
            $this->info('No accounts need country data backfill.');
            return 0;
        }

        $this->info("Found {$accounts->count()} accounts to process.");
        $this->newLine();

        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($accounts as $account) {
            try {
                $geoData = $this->geoService->getCountryFromIP($account->last_seen_ip);
                
                if ($geoData) {
                    $account->update([
                        'detected_country' => $geoData['country_code'],
                        'country_code' => $geoData['country_code'],
                        'country_name' => $geoData['country_name'],
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Failed to process account {$account->account_number}: {$e->getMessage()}");
                $failed++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Backfill completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updated],
                ['Skipped (no geo data)', $skipped],
                ['Failed', $failed],
                ['Total', $accounts->count()],
            ]
        );

        return 0;
    }
}
