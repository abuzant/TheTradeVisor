<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Convert an amount from one currency to another
     * 
     * @param float $amount The amount to convert
     * @param string $from Source currency code (e.g., 'AED')
     * @param string $to Target currency code (e.g., 'USD')
     * @return float Converted amount
     */
    public function convert(float $amount, string $from, string $to): float
    {
        // If same currency, no conversion needed
        if ($from === $to) {
            return $amount;
        }

        // Get the exchange rate
        $rate = $this->getRate($from, $to);
        
        // Convert and round to 2 decimal places
        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate between two currencies
     * Uses cache to avoid excessive API calls
     * 
     * @param string $from Source currency
     * @param string $to Target currency
     * @return float Exchange rate
     */
    public function getRate(string $from, string $to): float
    {
        $cacheKey = "currency_rate_{$from}_{$to}";
        
        // Check cache first (1 hour TTL)
        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            // Try to get from database (last 24 hours)
            $rate = CurrencyRate::where('from_currency', $from)
                ->where('to_currency', $to)
                ->where('updated_at', '>=', now()->subDay())
                ->latest('updated_at')
                ->first();

            if ($rate) {
                return (float) $rate->rate;
            }

            // If not in cache or DB, fetch fresh rate
            return $this->fetchAndStoreRate($from, $to);
        });
    }

    /**
     * Fetch rate from external API and store in database
     * 
     * @param string $from Source currency
     * @param string $to Target currency
     * @return float Exchange rate
     */
    private function fetchAndStoreRate(string $from, string $to): float
    {
        try {
            // Using exchangerate-api.com (Free tier: 1,500 requests/month)
            // Alternative free APIs:
            // - api.exchangerate.host (no key required)
            // - api.frankfurter.app (European Central Bank rates)
            
            $response = Http::timeout(5)
                ->get("https://api.exchangerate-api.com/v4/latest/{$from}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates'][$to])) {
                    $rate = (float) $data['rates'][$to];

                    // Store in database
                    CurrencyRate::create([
                        'from_currency' => $from,
                        'to_currency' => $to,
                        'rate' => $rate,
                        'updated_at' => now(),
                    ]);

                    Log::info("Currency rate fetched", [
                        'from' => $from,
                        'to' => $to,
                        'rate' => $rate
                    ]);

                    return $rate;
                }
            }
        } catch (\Exception $e) {
            Log::error("Currency rate fetch failed", [
                'from' => $from,
                'to' => $to,
                'error' => $e->getMessage()
            ]);
        }

        // Fallback: return 1.0 if API fails
        // This means no conversion, but app keeps working
        Log::warning("Using fallback rate 1.0 for {$from} to {$to}");
        return 1.0;
    }

    /**
     * Update all common currency pairs
     * Run this via cron job hourly
     */
    public function updateAllRates(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'AED', 'JPY', 'CHF', 'AUD', 'CAD', 'NZD', 'SGD'];
        
        Log::info("Starting currency rate update for all pairs");

        foreach ($currencies as $from) {
            foreach ($currencies as $to) {
                if ($from !== $to) {
                    try {
                        $this->fetchAndStoreRate($from, $to);
                        
                        // Sleep briefly to avoid rate limiting
                        usleep(100000); // 0.1 second
                    } catch (\Exception $e) {
                        Log::error("Failed to update {$from} to {$to}: {$e->getMessage()}");
                    }
                }
            }
        }

        Log::info("Currency rate update completed");
    }

    /**
     * Get all supported currencies
     * 
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'AED' => 'UAE Dirham',
            'JPY' => 'Japanese Yen',
            'CHF' => 'Swiss Franc',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
            'NZD' => 'New Zealand Dollar',
            'SGD' => 'Singapore Dollar',
        ];
    }
}
