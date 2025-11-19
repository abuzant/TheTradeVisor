<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnterpriseBroker;
use App\Models\WhitelistedBrokerUsage;
use App\Models\TradingAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnterpriseController extends Controller
{
    /**
     * Show comprehensive enterprise dashboard with all analytics
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Get timeframe from request (default: 30 days)
        // Only accept 7, 30, 90, 180 - default to 30 for any other value
        $days = (int) $request->input('days', 30);
        $validDays = [7, 30, 90, 180];
        if (!in_array($days, $validDays, true)) {
            $days = 30;
        }

        // Get all trading accounts for this broker
        $tradingAccounts = TradingAccount::where('broker_name', $broker->official_broker_name)
            ->with('user')
            ->get();

        $accountIds = $tradingAccounts->pluck('id')->toArray();
        $userIds = $tradingAccounts->pluck('user_id')->unique()->toArray();

        // Basic stats - CONVERT ALL TO USD for aggregated view
        $totalBalanceUSD = 0;
        $totalEquityUSD = 0;
        $totalProfitUSD = 0;

        foreach ($tradingAccounts as $account) {
            $totalBalanceUSD += $account->getBalanceInCurrency('USD');
            $totalEquityUSD += $account->getEquityInCurrency('USD');
            $totalProfitUSD += $account->getProfitInCurrency('USD');
        }

        $stats = [
            'total_users' => count($userIds),
            'total_accounts' => $tradingAccounts->count(),
            'active_last_7_days' => $tradingAccounts->where('last_data_received_at', '>=', now()->subDays(7))->count(),
            'total_balance' => $totalBalanceUSD,
            'total_equity' => $totalEquityUSD,
            'total_profit' => $totalProfitUSD,
        ];

        // Trading performance - CONVERT PROFITS TO USD (with 24h caching)
        $performance = [];
        if (!empty($accountIds)) {
            $cacheKey = "enterprise.performance.{$broker->id}.{$days}d";
            $performance = \Cache::remember($cacheKey, 86400, function () use ($accountIds, $days) {
                $deals = Deal::whereIn('trading_account_id', $accountIds)
                    ->where('time', '>=', now()->subDays($days))
                    ->where('entry', 'out')
                    ->whereIn('type', ['buy', 'sell'])
                    ->with('tradingAccount')
                    ->get();

                $totalProfitUSD = 0;
                $currencyService = app(\App\Services\CurrencyService::class);
                
                foreach ($deals as $deal) {
                    if ($deal->tradingAccount) {
                        $profitUSD = $currencyService->convert(
                            (float) $deal->profit,
                            $deal->tradingAccount->account_currency,
                            'USD'
                        );
                        $totalProfitUSD += $profitUSD;
                    }
                }

                $winningTrades = $deals->where('profit', '>', 0);
                $losingTrades = $deals->where('profit', '<', 0);
                $totalWinProfit = 0;
                $totalLossProfit = 0;
                
                foreach ($winningTrades as $trade) {
                    if ($trade->tradingAccount) {
                        $totalWinProfit += $currencyService->convert(
                            (float) $trade->profit,
                            $trade->tradingAccount->account_currency,
                            'USD'
                        );
                    }
                }
                
                foreach ($losingTrades as $trade) {
                    if ($trade->tradingAccount) {
                        $totalLossProfit += abs($currencyService->convert(
                            (float) $trade->profit,
                            $trade->tradingAccount->account_currency,
                            'USD'
                        ));
                    }
                }
                
                $profitFactor = $totalLossProfit > 0 ? round($totalWinProfit / $totalLossProfit, 2) : 0;
                
                // Find best and worst trades
                $bestTrade = null;
                $worstTrade = null;
                $bestProfit = 0;
                $worstProfit = 0;
                
                foreach ($deals as $deal) {
                    if ($deal->tradingAccount) {
                        $profitUSD = $currencyService->convert(
                            (float) $deal->profit,
                            $deal->tradingAccount->account_currency,
                            'USD'
                        );
                        
                        if ($profitUSD > $bestProfit) {
                            $bestProfit = $profitUSD;
                            $bestTrade = $deal;
                        }
                        
                        if ($profitUSD < $worstProfit) {
                            $worstProfit = $profitUSD;
                            $worstTrade = $deal;
                        }
                    }
                }

                return [
                    'total_trades' => $deals->count(),
                    'winning_trades' => $winningTrades->count(),
                    'losing_trades' => $losingTrades->count(),
                    'total_volume' => $deals->sum('volume'),
                    'total_profit' => $totalProfitUSD,
                    'win_rate' => $deals->count() > 0 ? round(($winningTrades->count() / $deals->count()) * 100, 2) : 0,
                    'profit_factor' => $profitFactor,
                    'best_trade' => $bestTrade ? [
                        'symbol' => $bestTrade->symbol,
                        'profit' => $bestProfit,
                        'volume' => $bestTrade->volume,
                        'date' => $bestTrade->time->format('M d, Y'),
                        'account_number' => $bestTrade->tradingAccount->account_number,
                        'account_currency' => $bestTrade->tradingAccount->account_currency,
                        'platform_type' => $bestTrade->tradingAccount->platform_type,
                    ] : null,
                    'worst_trade' => $worstTrade ? [
                        'symbol' => $worstTrade->symbol,
                        'profit' => $worstProfit,
                        'volume' => $worstTrade->volume,
                        'date' => $worstTrade->time->format('M d, Y'),
                        'account_number' => $worstTrade->tradingAccount->account_number,
                        'account_currency' => $worstTrade->tradingAccount->account_currency,
                        'platform_type' => $worstTrade->tradingAccount->platform_type,
                    ] : null,
                ];
            });
        }

        // Top performing accounts
        $topAccounts = $tradingAccounts->sortByDesc('profit')->take(10);

        // Recent activity
        $recentAccounts = $tradingAccounts->sortByDesc('last_data_received_at')->take(20);

        // Symbol performance - CONVERT PROFITS TO USD (with 24h caching)
        $symbolStats = collect();
        if (!empty($accountIds)) {
            $cacheKey = "enterprise.symbols.{$broker->id}.{$days}d";
            $symbolStats = \Cache::remember($cacheKey, 86400, function () use ($accountIds, $days) {
                $symbolDeals = Deal::whereIn('trading_account_id', $accountIds)
                    ->where('time', '>=', now()->subDays($days))
                    ->where('entry', 'out')
                    ->whereIn('type', ['buy', 'sell'])
                    ->with('tradingAccount')
                    ->get();

                $currencyService = app(\App\Services\CurrencyService::class);
                
                // Group by symbol and convert profits to USD
                $symbolGroups = $symbolDeals->groupBy('symbol');
                $symbolData = [];
                
                foreach ($symbolGroups as $symbol => $deals) {
                    $totalProfitUSD = 0;
                    foreach ($deals as $deal) {
                        if ($deal->tradingAccount) {
                            $profitUSD = $currencyService->convert(
                                (float) $deal->profit,
                                $deal->tradingAccount->account_currency,
                                'USD'
                            );
                            $totalProfitUSD += $profitUSD;
                        }
                    }
                    
                    $symbolData[] = (object)[
                        'symbol' => $symbol,
                        'normalized_symbol' => \App\Models\SymbolMapping::normalize($symbol),
                        'trade_count' => $deals->count(),
                        'total_profit' => $totalProfitUSD,
                        'total_volume' => $deals->sum('volume'),
                        'winning_trades' => $deals->where('profit', '>', 0)->count(),
                    ];
                }
                
                // Sort by profit and take top 10
                return collect($symbolData)->sortByDesc('total_profit')->take(10);
            });
        }

        // Balance & Equity chart data - CONVERT TO USD (with 24h caching)
        $chartData = [];
        if (!empty($accountIds)) {
            $cacheKey = "enterprise.chart.{$broker->id}.{$days}d";
            $chartData = \Cache::remember($cacheKey, 86400, function () use ($accountIds, $days, $tradingAccounts) {
                $currencyService = app(\App\Services\CurrencyService::class);
                
                // Get unique dates
                $dates = \App\Models\AccountSnapshot::whereIn('trading_account_id', $accountIds)
                    ->where('snapshot_time', '>=', now()->subDays($days))
                    ->selectRaw('DATE(snapshot_time) as date')
                    ->distinct()
                    ->orderBy('date')
                    ->pluck('date');
                
                // For each date, get the LATEST snapshot per account and aggregate
                $dailyData = [];
                foreach ($dates as $date) {
                    $totalBalanceUSD = 0;
                    $totalEquityUSD = 0;
                    
                    foreach ($accountIds as $accountId) {
                        // Get the LATEST snapshot for this account on this date
                        $snapshot = \App\Models\AccountSnapshot::where('trading_account_id', $accountId)
                            ->whereDate('snapshot_time', $date)
                            ->orderBy('snapshot_time', 'desc')
                            ->first();
                        
                        if ($snapshot) {
                            $account = $tradingAccounts->firstWhere('id', $accountId);
                            if ($account) {
                                // Convert to USD
                                $balanceUSD = $currencyService->convert(
                                    (float) $snapshot->balance,
                                    $account->account_currency,
                                    'USD'
                                );
                                $equityUSD = $currencyService->convert(
                                    (float) $snapshot->equity,
                                    $account->account_currency,
                                    'USD'
                                );
                                
                                $totalBalanceUSD += $balanceUSD;
                                $totalEquityUSD += $equityUSD;
                            }
                        }
                    }
                    
                    $dailyData[] = [
                        'date' => $date,
                        'balance' => round($totalBalanceUSD, 2),
                        'equity' => round($totalEquityUSD, 2),
                    ];
                }
                
                return $dailyData;
            });
        }

        return view('enterprise.dashboard', compact(
            'broker', 
            'stats', 
            'performance', 
            'topAccounts', 
            'recentAccounts',
            'symbolStats',
            'chartData',
            'tradingAccounts',
            'days'
        ));
    }

    /**
     * Show enterprise settings
     */
    public function settings()
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        return view('enterprise.settings', compact('broker'));
    }

    /**
     * Update enterprise settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'official_broker_name' => 'required|string|max:255',
        ]);

        $broker->update([
            'company_name' => $request->company_name,
            'official_broker_name' => $request->official_broker_name,
        ]);

        return back()->with('success', 'Settings updated successfully');
    }

}
