<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CircuitBreakerService;

class AnalyticsControllerOptimized extends Controller
{
    protected CircuitBreakerService $circuitBreaker;
    
    public function __construct(CircuitBreakerService $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
    }
    
    /**
     * CRITICAL FIX: Regional Activity with proper query optimization
     * 
     * BEFORE: Loaded ALL accounts into memory with ->get()
     * AFTER: Uses database aggregation with limits
     */
    private function getRegionalActivityOptimized($days = 30)
    {
        // Check if we have country data
        $hasCountryCode = TradingAccount::whereNotNull('country_code')->exists();
        $hasDetectedCountry = TradingAccount::whereNotNull('detected_country')->exists();
        
        if ($hasCountryCode) {
            // Use database aggregation instead of loading all records
            $data = TradingAccount::select(
                    'country_code',
                    'country_name',
                    DB::raw('COUNT(*) as accounts'),
                    DB::raw('SUM(balance) as total_balance'),
                    'account_currency'
                )
                ->where('is_active', true)
                ->whereNotNull('country_code')
                ->groupBy('country_code', 'country_name', 'account_currency')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(20) // CRITICAL: Add limit
                ->get();
                
            // Group by country and aggregate
            $grouped = $data->groupBy('country_code');
            
            $result = $grouped->map(function($countryData, $countryCode) {
                $currencyService = app(\App\Services\CurrencyService::class);
                $totalBalanceUSD = 0;
                $accountCount = 0;
                
                foreach ($countryData as $row) {
                    $balanceUSD = $currencyService->convert(
                        $row->total_balance,
                        $row->account_currency ?? 'USD',
                        'USD'
                    );
                    $totalBalanceUSD += $balanceUSD;
                    $accountCount += $row->accounts;
                }
                
                return [
                    'country' => $countryData->first()->country_name ?? $countryCode,
                    'country_code' => $countryCode,
                    'accounts' => $accountCount,
                    'balance' => round($totalBalanceUSD, 2),
                ];
            })->sortByDesc('accounts')->take(10)->values();
            
            return $result;
            
        } elseif ($hasDetectedCountry) {
            // Similar optimization for detected_country
            $data = TradingAccount::select(
                    'detected_country',
                    DB::raw('COUNT(*) as accounts'),
                    DB::raw('SUM(balance) as total_balance'),
                    'account_currency'
                )
                ->where('is_active', true)
                ->whereNotNull('detected_country')
                ->groupBy('detected_country', 'account_currency')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(20) // CRITICAL: Add limit
                ->get();
                
            $grouped = $data->groupBy('detected_country');
            
            $result = $grouped->map(function($countryData, $country) {
                $currencyService = app(\App\Services\CurrencyService::class);
                $totalBalanceUSD = 0;
                $accountCount = 0;
                
                foreach ($countryData as $row) {
                    $balanceUSD = $currencyService->convert(
                        $row->total_balance,
                        $row->account_currency ?? 'USD',
                        'USD'
                    );
                    $totalBalanceUSD += $balanceUSD;
                    $accountCount += $row->accounts;
                }
                
                return [
                    'country' => $country,
                    'country_code' => null,
                    'accounts' => $accountCount,
                    'balance' => round($totalBalanceUSD, 2),
                ];
            })->sortByDesc('accounts')->take(10)->values();
            
            return $result;
            
        } else {
            // Fallback: Use aggregation instead of loading all accounts
            $stats = TradingAccount::select(
                    DB::raw('COUNT(*) as accounts'),
                    DB::raw('SUM(balance) as total_balance')
                )
                ->where('is_active', true)
                ->first();
                
            return collect([
                [
                    'country' => 'No location data available',
                    'country_code' => null,
                    'accounts' => $stats->accounts ?? 0,
                    'balance' => round($stats->total_balance ?? 0, 2),
                    'note' => 'Country detection requires IP geolocation to be enabled'
                ]
            ]);
        }
    }
    
    /**
     * CRITICAL FIX: Symbol Country Heatmap with limits
     */
    private function getSymbolCountryHeatmapOptimized($days = 30)
    {
        $data = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'deals.symbol',
                'trading_accounts.country_code',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades')
            )
            ->whereNotNull('trading_accounts.country_code')
            ->whereNotNull('deals.symbol')
            ->where('deals.symbol', '!=', '')
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy('deals.symbol', 'trading_accounts.country_code')
            ->havingRaw('COUNT(*) >= 1')
            ->orderBy('total_trades', 'desc')
            ->limit(50) // CRITICAL: Reduced from 100 to 50
            ->get();

        return $data->map(function($item) {
            $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
            
            return [
                'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                'country_code' => $item->country_code,
                'total_trades' => $item->total_trades,
                'total_profit' => round($item->total_profit, 2),
                'win_rate' => $winRate,
            ];
        });
    }
    
    /**
     * CRITICAL FIX: Trading Session Analysis with query timeout protection
     */
    private function getTradingSessionAnalysisOptimized($days = 30)
    {
        // Set query timeout
        DB::statement("SET LOCAL statement_timeout = '10s'");
        
        try {
            $data = Deal::select(
                    'symbol',
                    DB::raw('EXTRACT(HOUR FROM time) as hour'),
                    DB::raw('COUNT(*) as trades'),
                    DB::raw('SUM(profit) as profit')
                )
                ->whereNotNull('symbol')
                ->where('symbol', '!=', '')
                ->where('time', '>=', now()->subDays($days))
                ->groupBy('symbol', 'hour')
                ->havingRaw('COUNT(*) >= 5') // CRITICAL: Filter before loading
                ->orderBy('symbol')
                ->orderBy('hour')
                ->limit(200) // CRITICAL: Add limit
                ->get();

            $sessions = [
                'Sydney' => ['start' => 0, 'end' => 8],
                'Tokyo' => ['start' => 0, 'end' => 9],
                'London' => ['start' => 8, 'end' => 16],
                'New York' => ['start' => 13, 'end' => 22],
            ];

            $result = [];
            foreach ($sessions as $sessionName => $timeRange) {
                $sessionData = $data->filter(function($item) use ($timeRange) {
                    return $item->hour >= $timeRange['start'] && $item->hour <= $timeRange['end'];
                });

                $result[$sessionName] = [
                    'trades' => $sessionData->sum('trades'),
                    'profit' => round($sessionData->sum('profit'), 2),
                    'symbols' => $sessionData->pluck('symbol')->unique()->count(),
                ];
            }

            return $result;
            
        } catch (\Exception $e) {
            // Log timeout and return empty result
            \Log::warning('Trading session analysis timed out', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
