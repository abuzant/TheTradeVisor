<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    /**
     * Global analytics dashboard
     */
    public function index(Request $request)
    {
        // User must have at least one active account to view global analytics
        $user = $request->user();

        if ($user->tradingAccounts()->where('is_active', true)->count() === 0) {
            return view('analytics.locked');
        }

        $days = $request->get('days', 30);
        $displayCurrency = $user->display_currency ?? 'USD';

        // Cache analytics for 5 minutes with days parameter
        $analytics = Cache::remember("global_analytics_{$days}", 300, function () use ($days) {
            return [
                'overview' => $this->getOverviewStats($days),
                'popular_pairs' => $this->getPopularPairs($days),
                'trading_by_hour' => $this->getTradingByHour($days),
                'regional_activity' => $this->getRegionalActivity(),
                'broker_distribution' => $this->getBrokerDistribution(),
                'sentiment' => $this->getMarketSentiment(),
                'top_performers' => $this->getTopPerformers($days),
                'daily_volume_trend' => $this->getDailyVolumeTrend($days),
                'win_rate_by_symbol' => $this->getWinRateBySymbol($days),
                'trading_costs' => $this->getTradingCosts($days),
                'position_sizes' => $this->getPositionSizeDistribution($days),
                'trade_duration' => $this->getTradeDurationStats($days),
            ];
        });

        return view('analytics.index', compact('analytics', 'days', 'displayCurrency'));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats($days = 30)
    {
        return [
            'total_traders' => TradingAccount::distinct('user_id')->count('user_id'),
            'active_accounts' => TradingAccount::where('is_active', true)->count(),
            'total_brokers' => TradingAccount::distinct('broker_name')->count('broker_name'),
            'open_positions' => Position::where('is_open', true)->count(),
            'total_volume' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->sum('volume'),
            'total_trades' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->count(),
            'total_profit' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->sum('profit'),
            'avg_trade_profit' => Deal::whereNotNull('symbol')
                                      ->where('symbol', '!=', '')
                                      ->where('time', '>=', now()->subDays($days))
                                      ->avg('profit'),
            'countries' => TradingAccount::whereNotNull('detected_country')->distinct('detected_country')->count('detected_country'),
        ];
    }

    /**
     * Get most popular trading pairs
     */
    private function getPopularPairs($days = 30)
    {
        return Deal::select('symbol', DB::raw('COUNT(*) as trades'), DB::raw('SUM(volume) as total_volume'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('symbol', '!=', 'UNKNOWN')
            ->where('time', '>=', now()->subDays($days))
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [3])
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'raw_symbol' => $item->symbol,
                    'trades' => $item->trades,
                    'volume' => round($item->total_volume, 2),
                ];
            });
    }

    /**
     * Get trading activity by hour (UTC)
     */
    private function getTradingByHour($days = 30)
    {
        $data = Deal::select(DB::raw('EXTRACT(HOUR FROM time) as hour'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
        ->where('time', '>=', now()->subDays($days))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[] = [
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                'trades' => $data->get($i, 0),
            ];
        }

        return $result;
    }

    /**
     * Get regional trading activity
     */
    private function getRegionalActivity()
    {
        return TradingAccount::select('detected_country', DB::raw('COUNT(*) as accounts'), DB::raw('SUM(balance) as total_balance'))
            ->whereNotNull('detected_country')
            ->where('is_active', true)
            ->groupBy('detected_country')
            ->orderBy('accounts', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'country' => $item->detected_country,
                    'accounts' => $item->accounts,
                    'balance' => round($item->total_balance, 2),
                ];
            });
    }

    /**
     * Get broker distribution
     */
    private function getBrokerDistribution()
    {
        return TradingAccount::select('broker_name', DB::raw('COUNT(*) as accounts'))
            ->where('is_active', true)
            ->groupBy('broker_name')
            ->orderBy('accounts', 'desc')
            ->limit(8)
            ->get();
    }

    /**
     * Get market sentiment (buy vs sell positions)
     */
    private function getMarketSentiment()
    {
        $positions = Position::select('symbol', 'type', DB::raw('COUNT(*) as count'))
            ->where('is_open', true)
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->groupBy('symbol', 'type')
            ->get();

        $sentiment = [];

        foreach ($positions as $position) {
            $normalizedSymbol = \App\Models\SymbolMapping::normalize($position->symbol);
            if (!isset($sentiment[$normalizedSymbol])) {
                $sentiment[$normalizedSymbol] = ['buy' => 0, 'sell' => 0];
            }
            // Accumulate counts for each type
            $sentiment[$normalizedSymbol][$position->type] += $position->count;
        }

        // Calculate sentiment percentage
        $result = [];
        foreach ($sentiment as $symbol => $data) {
            $total = $data['buy'] + $data['sell'];
            if ($total > 5) { // Only show symbols with significant activity
                $result[] = [
                    'symbol' => $symbol,
                    'buy_percent' => round(($data['buy'] / $total) * 100, 1),
                    'sell_percent' => round(($data['sell'] / $total) * 100, 1),
                    'total' => $total,
                ];
            }
        }

        // Sort by total positions
        usort($result, function($a, $b) {
            return $b['total'] - $a['total'];
        });

        return array_slice($result, 0, 10);
    }

    /**
     * Get top performing symbols (by profit)
     */
    private function getTopPerformers($days = 30)
    {
        return Deal::select('symbol', DB::raw('SUM(profit) as total_profit'), DB::raw('COUNT(*) as trades'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('symbol', '!=', 'UNKNOWN')
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['out', 'inout'])
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [3])
            ->orderByRaw('SUM(profit) DESC')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'raw_symbol' => $item->symbol,
                    'profit' => round($item->total_profit, 2),
                    'trades' => $item->trades,
                    'avg_profit' => round($item->total_profit / $item->trades, 2),
                ];
            });
    }

    /**
     * Get daily volume trend
     */
    private function getDailyVolumeTrend($days = 30)
    {
        return Deal::select(DB::raw('DATE(time) as date'), DB::raw('SUM(volume) as volume'), DB::raw('COUNT(*) as trades'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get win rate by symbol
     */
    private function getWinRateBySymbol($days = 30)
    {
        return Deal::select('symbol',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(profit) as total_profit'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['out', 'inout'])
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [5])
            ->get()
            ->map(function($item) {
                $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'total_trades' => $item->total_trades,
                    'win_rate' => $winRate,
                    'total_profit' => round($item->total_profit, 2),
                ];
            })
            ->sortByDesc('win_rate')
            ->take(10)
            ->values();
    }

    /**
     * Get trading costs analysis
     */
    private function getTradingCosts($days = 30)
    {
        $deals = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->select(DB::raw('SUM(commission) as total_commission'),
                    DB::raw('SUM(swap) as total_swap'),
                    DB::raw('COUNT(*) as total_trades'))
            ->first();

        return [
            'total_commission' => round($deals->total_commission ?? 0, 2),
            'total_swap' => round($deals->total_swap ?? 0, 2),
            'total_costs' => round(($deals->total_commission ?? 0) + ($deals->total_swap ?? 0), 2),
            'avg_cost_per_trade' => $deals->total_trades > 0 ? round((($deals->total_commission ?? 0) + ($deals->total_swap ?? 0)) / $deals->total_trades, 2) : 0,
        ];
    }

    /**
     * Get position size distribution
     */
    private function getPositionSizeDistribution($days = 30)
    {
        $sizes = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->pluck('volume');

        return [
            'avg' => round($sizes->avg(), 2),
            'min' => round($sizes->min(), 2),
            'max' => round($sizes->max(), 2),
            'median' => round($sizes->median(), 2),
        ];
    }

    /**
     * Get trade duration statistics
     */
    private function getTradeDurationStats($days = 30)
    {
        // Get positions that were closed in the period
        $closedPositions = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->count();

        return [
            'total_closed' => $closedPositions,
            'avg_duration' => 'N/A', // Would need position open/close time tracking
        ];
    }
}
