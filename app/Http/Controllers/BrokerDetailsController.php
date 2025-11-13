<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\Position;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BrokerDetailsController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Show public broker analytics page
     * Aggregated data from all users (last 180 days)
     * Cached for 4 hours for performance
     */
    public function show(Request $request, $broker)
    {
        $broker = urldecode($broker);
        $days = 180; // Fixed to 180 days for public view

        // Check if broker exists
        $brokerExists = TradingAccount::where('broker_name', $broker)->exists();
        if (!$brokerExists) {
            abort(404, 'Broker not found');
        }

        // Cache key for this broker
        $cacheKey = "broker.public.{$broker}.180d";
        
        // Get cached data or compute (4 hour cache)
        $data = Cache::remember($cacheKey, 14400, function() use ($broker, $days) {
            // Get all account IDs for this broker
            $accountIds = TradingAccount::where('broker_name', $broker)->pluck('id');
            
            if ($accountIds->isEmpty()) {
                return null;
            }

            return [
                'overview' => $this->getOverviewStats($accountIds, $days),
                'top_countries' => $this->getTopCountries($accountIds, $days),
                'most_profitable_pairs' => $this->getMostProfitablePairs($accountIds, $days),
                'biggest_loss_pairs' => $this->getBiggestLossPairs($accountIds, $days),
                'daily_profit_trend' => $this->getDailyProfitTrend($accountIds, $days),
                'top_symbols' => $this->getTopSymbols($accountIds, $days),
                'symbol_performance' => $this->getSymbolPerformance($accountIds, $days),
                'avg_hold_time' => $this->getAverageHoldTime($accountIds, $days),
            ];
        });

        if (!$data) {
            abort(404, 'No data available for this broker');
        }

        return view('broker-details.show', array_merge($data, [
            'broker' => $broker,
            'days' => $days,
        ]));
    }

    /**
     * Overview statistics
     */
    private function getOverviewStats($accountIds, $days)
    {
        $stats = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->select(
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(profit) as total_profit_native'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('SUM(commission) as total_commission'),
                DB::raw('SUM(swap) as total_swap'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('AVG(volume) as avg_trade_size'),
                DB::raw('COUNT(DISTINCT trading_account_id) as active_traders')
            )
            ->first();

        // Convert to USD for display
        $totalProfitUSD = 0;
        $accounts = TradingAccount::whereIn('id', $accountIds)->get();
        foreach ($accounts as $account) {
            $accountProfit = Deal::where('trading_account_id', $account->id)
                ->where('time', '>=', now()->subDays($days))
                ->where('entry', 'out')
                ->sum('profit');
            $totalProfitUSD += $this->currencyService->convert(
                $accountProfit,
                $account->account_currency ?? 'USD',
                'USD'
            );
        }

        return [
            'total_trades' => $stats->total_trades ?? 0,
            'total_profit' => $totalProfitUSD,
            'total_volume' => $stats->total_volume ?? 0,
            'total_commission' => $stats->total_commission ?? 0,
            'total_swap' => $stats->total_swap ?? 0,
            'win_rate' => $stats->total_trades > 0 ? round(($stats->winning_trades / $stats->total_trades) * 100, 1) : 0,
            'avg_trade_size' => $stats->avg_trade_size ?? 0,
            'active_traders' => $stats->active_traders ?? 0,
        ];
    }

    /**
     * Top trading countries
     */
    private function getTopCountries($accountIds, $days)
    {
        // Get deals with country info from trading accounts
        // Use country_name or detected_country
        return Deal::whereIn('trading_account_id', $accountIds)
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->where('deals.time', '>=', now()->subDays($days))
            ->where('deals.entry', 'out')
            ->where(function($query) {
                $query->whereNotNull('trading_accounts.country_name')
                      ->orWhereNotNull('trading_accounts.detected_country');
            })
            ->select(
                DB::raw('COALESCE(trading_accounts.country_name, trading_accounts.detected_country) as country'),
                DB::raw('COUNT(*) as trades'),
                DB::raw('SUM(deals.profit) as profit'),
                DB::raw('SUM(deals.volume) as volume')
            )
            ->groupBy('country')
            ->orderByDesc('trades')
            ->limit(10)
            ->get();
    }

    /**
     * Most profitable pairs
     */
    private function getMostProfitablePairs($accountIds, $days)
    {
        return Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->whereNotNull('symbol')
            ->select(
                'symbol',
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('COUNT(*) as trades'),
                DB::raw('AVG(volume) as avg_volume')
            )
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= 5')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->normalized_symbol = \App\Models\SymbolMapping::normalize($item->symbol);
                return $item;
            });
    }

    /**
     * Biggest loss pairs
     */
    private function getBiggestLossPairs($accountIds, $days)
    {
        return Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->whereNotNull('symbol')
            ->select(
                'symbol',
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('COUNT(*) as trades'),
                DB::raw('AVG(volume) as avg_volume')
            )
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= 5')
            ->orderBy('total_profit', 'asc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->normalized_symbol = \App\Models\SymbolMapping::normalize($item->symbol);
                return $item;
            });
    }

    /**
     * Daily profit trend
     */
    private function getDailyProfitTrend($accountIds, $days)
    {
        return Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->select(
                DB::raw('DATE(time) as date'),
                DB::raw('SUM(profit) as profit'),
                DB::raw('COUNT(*) as trades')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Top traded symbols
     */
    private function getTopSymbols($accountIds, $days)
    {
        return Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->whereNotNull('symbol')
            ->select(
                'symbol',
                DB::raw('COUNT(*) as trades'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('AVG(volume) as avg_lot_size')
            )
            ->groupBy('symbol')
            ->orderByDesc('trades')
            ->limit(25)
            ->get()
            ->map(function($item) {
                $item->normalized_symbol = \App\Models\SymbolMapping::normalize($item->symbol);
                return $item;
            });
    }

    /**
     * Symbol performance (for top 25 symbols)
     */
    private function getSymbolPerformance($accountIds, $days)
    {
        // Get top 25 symbols first
        $topSymbols = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->select('symbol', DB::raw('COUNT(*) as trades'))
            ->groupBy('symbol')
            ->orderByDesc('trades')
            ->limit(25)
            ->pluck('symbol');

        return Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->whereIn('symbol', $topSymbols)
            ->select(
                'symbol',
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('AVG(volume) as avg_lot_size')
            )
            ->groupBy('symbol')
            ->get()
            ->map(function($item) {
                $item->normalized_symbol = \App\Models\SymbolMapping::normalize($item->symbol);
                $item->win_rate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
                return $item;
            });
    }

    /**
     * Average hold time
     */
    private function getAverageHoldTime($accountIds, $days)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['in', 'out'])
            ->orderBy('time', 'asc')
            ->get(['position_id', 'entry', 'time']);

        $grouped = $deals->groupBy('position_id');
        $holdTimes = [];

        foreach ($grouped as $positionDeals) {
            $inDeal = $positionDeals->where('entry', 'in')->first();
            $outDeal = $positionDeals->where('entry', 'out')->first();
            
            if ($inDeal && $outDeal) {
                $hours = \Carbon\Carbon::parse($inDeal->time)->diffInHours(\Carbon\Carbon::parse($outDeal->time));
                $holdTimes[] = $hours;
            }
        }

        if (empty($holdTimes)) {
            return 'N/A';
        }

        $avgHours = array_sum($holdTimes) / count($holdTimes);

        if ($avgHours < 1) {
            return round($avgHours * 60) . ' min';
        } elseif ($avgHours < 24) {
            return round($avgHours, 1) . ' hrs';
        } else {
            $days = floor($avgHours / 24);
            $hours = round($avgHours % 24);
            return $days . 'd ' . $hours . 'h';
        }
    }
}
