<?php

namespace App\Http\Controllers;

use App\Models\ApiRequestLog;
use App\Models\Deal;
use App\Models\TradingAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CountryAnalyticsController extends Controller
{
    /**
     * Get top trading countries with statistics
     */
    public function topTradingCountries(Request $request)
    {
        $days = $request->input('days', 30);

        $countries = Cache::remember("global_country_analytics_{$days}", 3600, function () use ($days) {
            $startDate = now()->subDays($days);

            // Get countries from all trading accounts with deal statistics
            $countries = TradingAccount::where('is_active', true)
                ->whereNotNull('country_code')
                ->select('country_code', 'country_name')
                ->selectRaw('COUNT(DISTINCT trading_accounts.id) as account_count, SUM(balance) as total_balance')
                ->leftJoin('deals', 'trading_accounts.id', '=', 'deals.trading_account_id')
                ->where(function ($query) use ($startDate) {
                    $query->whereNull('deals.time_close')
                        ->orWhere('deals.time_close', '>=', $startDate);
                })
                ->groupBy('country_code', 'country_name')
                ->get()
                ->map(function ($country) use ($startDate) {
                    // Get deal statistics for this country
                    $stats = Deal::whereHas('tradingAccount', function ($query) use ($country) {
                        $query->where('country_code', $country->country_code);
                    })
                    ->where('time_close', '>=', $startDate)
                    ->selectRaw('
                        COUNT(*) as total_trades,
                        SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades,
                        SUM(profit) as total_profit,
                        AVG(profit) as avg_profit
                    ')
                    ->first();

                    return [
                        'country_code' => $country->country_code,
                        'country_name' => $country->country_name,
                        'account_count' => $country->account_count,
                        'total_balance' => round($country->total_balance ?? 0, 2),
                        'total_trades' => $stats->total_trades ?? 0,
                        'winning_trades' => $stats->winning_trades ?? 0,
                        'win_rate' => $stats->total_trades > 0 
                            ? round(($stats->winning_trades / $stats->total_trades) * 100, 2) 
                            : 0,
                        'total_profit' => round($stats->total_profit ?? 0, 2),
                        'avg_profit' => round($stats->avg_profit ?? 0, 2),
                    ];
                })
                ->sortByDesc('total_trades')
                ->values();

            return $countries;
        });

        return view('analytics.countries', compact('countries', 'days'));
    }

    /**
     * Get country distribution for a specific symbol
     */
    public function countryBySymbol(Request $request, string $symbol)
    {
        $userId = auth()->id();
        $days = $request->input('days', 30);

        return Cache::remember("country_symbol_{$userId}_{$symbol}_{$days}", 3600, function () use ($userId, $symbol, $days) {
            $startDate = now()->subDays($days);

            $countries = Deal::whereHas('tradingAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereNotNull('country_code');
            })
            ->where('symbol', $symbol)
            ->where('time_close', '>=', $startDate)
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select('trading_accounts.country_code', 'trading_accounts.country_name')
            ->selectRaw('
                COUNT(*) as trade_count,
                SUM(deals.profit) as total_profit,
                SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades
            ')
            ->groupBy('trading_accounts.country_code', 'trading_accounts.country_name')
            ->get()
            ->map(function ($country) {
                return [
                    'country_code' => $country->country_code,
                    'country_name' => $country->country_name,
                    'trade_count' => $country->trade_count,
                    'total_profit' => round($country->total_profit, 2),
                    'win_rate' => $country->trade_count > 0 
                        ? round(($country->winning_trades / $country->trade_count) * 100, 2) 
                        : 0,
                ];
            })
            ->sortByDesc('trade_count')
            ->values();

            return $countries;
        });
    }

    /**
     * Get country distribution for a specific broker
     */
    public function countryByBroker(Request $request, string $broker)
    {
        $userId = auth()->id();
        $days = $request->input('days', 30);

        return Cache::remember("country_broker_{$userId}_{$broker}_{$days}", 3600, function () use ($userId, $broker, $days) {
            $startDate = now()->subDays($days);

            $countries = TradingAccount::where('user_id', $userId)
                ->where('broker_name', $broker)
                ->whereNotNull('country_code')
                ->select('country_code', 'country_name')
                ->selectRaw('COUNT(*) as account_count')
                ->groupBy('country_code', 'country_name')
                ->get()
                ->map(function ($country) use ($userId, $broker, $startDate) {
                    // Get deal statistics
                    $stats = Deal::whereHas('tradingAccount', function ($query) use ($userId, $broker, $country) {
                        $query->where('user_id', $userId)
                            ->where('broker_name', $broker)
                            ->where('country_code', $country->country_code);
                    })
                    ->where('time_close', '>=', $startDate)
                    ->selectRaw('
                        COUNT(*) as total_trades,
                        SUM(profit) as total_profit
                    ')
                    ->first();

                    return [
                        'country_code' => $country->country_code,
                        'country_name' => $country->country_name,
                        'account_count' => $country->account_count,
                        'total_trades' => $stats->total_trades ?? 0,
                        'total_profit' => round($stats->total_profit ?? 0, 2),
                    ];
                })
                ->sortByDesc('account_count')
                ->values();

            return $countries;
        });
    }

    /**
     * Get trading patterns for a specific country
     */
    public function countryTradingPatterns(Request $request, string $countryCode)
    {
        $userId = auth()->id();
        $days = $request->input('days', 90);

        return Cache::remember("country_patterns_{$userId}_{$countryCode}_{$days}", 3600, function () use ($userId, $countryCode, $days) {
            $startDate = now()->subDays($days);

            // Day of week distribution
            $dayOfWeek = Deal::whereHas('tradingAccount', function ($query) use ($userId, $countryCode) {
                $query->where('user_id', $userId)
                    ->where('country_code', $countryCode);
            })
            ->where('time_close', '>=', $startDate)
            ->selectRaw('DAYOFWEEK(time_close) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

            // Popular symbols
            $popularSymbols = Deal::whereHas('tradingAccount', function ($query) use ($userId, $countryCode) {
                $query->where('user_id', $userId)
                    ->where('country_code', $countryCode);
            })
            ->where('time_close', '>=', $startDate)
            ->select('symbol')
            ->selectRaw('COUNT(*) as trade_count, SUM(profit) as total_profit')
            ->groupBy('symbol')
            ->orderByDesc('trade_count')
            ->limit(10)
            ->get();

            // Preferred brokers
            $preferredBrokers = TradingAccount::where('user_id', $userId)
                ->where('country_code', $countryCode)
                ->select('broker_name')
                ->selectRaw('COUNT(*) as account_count')
                ->groupBy('broker_name')
                ->orderByDesc('account_count')
                ->get();

            return [
                'day_of_week' => $dayOfWeek,
                'popular_symbols' => $popularSymbols,
                'preferred_brokers' => $preferredBrokers,
            ];
        });
    }
}
