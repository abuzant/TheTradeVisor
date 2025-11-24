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
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

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
     * Show enterprise analytics page
     */
    public function analytics(Request $request)
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Get timeframe from request (default: 30 days)
        $days = (int) $request->input('days', 30);
        $validDays = [7, 30, 90, 180];
        if (!in_array($days, $validDays, true)) {
            $days = 30;
        }

        // Get all trading accounts for this broker
        $accountIds = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->pluck('trading_account_id')
            ->toArray();

        // Country distribution
        $countryStats = TradingAccount::whereIn('id', $accountIds)
            ->selectRaw('country_code, country_name, COUNT(*) as account_count')
            ->whereNotNull('country_code')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('account_count')
            ->get();

        // Platform distribution
        $platformStats = TradingAccount::whereIn('id', $accountIds)
            ->selectRaw('platform_type, COUNT(*) as account_count')
            ->groupBy('platform_type')
            ->get();

        // Symbol performance
        $symbolPerformance = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->selectRaw('symbol, COUNT(*) as trade_count, SUM(profit) as total_profit')
            ->groupBy('symbol')
            ->orderByDesc('total_profit')
            ->limit(20)
            ->get();

        // Get all accounts with their data
        $accounts = TradingAccount::whereIn('id', $accountIds)->get();
        
        // Get all deals for the period
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->get();

        // Calculate comprehensive stats
        $stats = [
            // User & Account Stats
            'total_users' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)->distinct('user_id')->count('user_id'),
            'total_accounts' => count($accountIds),
            'active_accounts_7d' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)->where('last_seen_at', '>=', now()->subDays(7))->count(),
            'active_accounts_30d' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)->where('last_seen_at', '>=', now()->subDays(30))->count(),
            
            // Trading Volume Stats
            'total_trades' => $deals->count(),
            'winning_trades' => $deals->where('profit', '>', 0)->count(),
            'losing_trades' => $deals->where('profit', '<', 0)->count(),
            'breakeven_trades' => $deals->where('profit', '=', 0)->count(),
            
            // Profit Stats
            'total_profit' => $deals->sum('profit'),
            'total_commission' => $deals->sum('commission'),
            'total_swap' => $deals->sum('swap'),
            'net_profit' => $deals->sum('profit') + $deals->sum('commission') + $deals->sum('swap'),
            'avg_profit_per_trade' => $deals->count() > 0 ? $deals->avg('profit') : 0,
            'best_trade' => $deals->max('profit'),
            'worst_trade' => $deals->min('profit'),
            
            // Win Rate & Performance
            'win_rate' => $deals->count() > 0 ? ($deals->where('profit', '>', 0)->count() / $deals->count()) * 100 : 0,
            'avg_win' => $deals->where('profit', '>', 0)->avg('profit') ?? 0,
            'avg_loss' => abs($deals->where('profit', '<', 0)->avg('profit') ?? 0),
            'profit_factor' => abs($deals->where('profit', '<', 0)->sum('profit')) > 0 ? $deals->where('profit', '>', 0)->sum('profit') / abs($deals->where('profit', '<', 0)->sum('profit')) : 0,
            
            // Account Balance Stats
            'total_balance' => $accounts->sum('balance'),
            'total_equity' => $accounts->sum('equity'),
            'avg_balance' => $accounts->avg('balance'),
            'avg_equity' => $accounts->avg('equity'),
            'max_balance' => $accounts->max('balance'),
            'min_balance' => $accounts->min('balance'),
            
            // Leverage & Margin
            'avg_leverage' => $accounts->avg('leverage'),
            'total_margin_used' => $accounts->sum('margin'),
            'avg_margin_level' => $accounts->where('margin', '>', 0)->avg('margin_level'),
            
            // Volume Stats
            'total_volume' => $deals->sum('volume'),
            'avg_volume_per_trade' => $deals->count() > 0 ? $deals->avg('volume') : 0,
            'max_volume_trade' => $deals->max('volume'),
            
            // Most Traded Symbols
            'most_traded_symbol' => $deals->groupBy('symbol')->sortByDesc(fn($group) => $group->count())->keys()->first(),
            'most_profitable_symbol' => $deals->groupBy('symbol')->sortByDesc(fn($group) => $group->sum('profit'))->keys()->first(),
        ];

        // Get users with pagination
        $users = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->with(['user', 'tradingAccount'])
            ->orderBy('last_seen_at', 'desc')
            ->paginate(20);

        return view('enterprise.analytics', compact(
            'broker',
            'days',
            'countryStats',
            'platformStats',
            'symbolPerformance',
            'stats',
            'users'
        ));
    }

    /**
     * Show enterprise accounts page
     */
    public function accounts(Request $request)
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Get filters
        $platform = $request->input('platform', 'all');
        $country = $request->input('country', 'all');
        $status = $request->input('status', 'all');

        // Get all accounts for this broker
        $query = TradingAccount::whereIn('id', function($q) use ($broker) {
            $q->select('trading_account_id')
              ->from('whitelisted_broker_usage')
              ->where('enterprise_broker_id', $broker->id);
        });

        // Apply filters
        if ($platform !== 'all') {
            $query->where('platform_type', strtoupper($platform));
        }

        if ($country !== 'all') {
            $query->where('country_code', strtoupper($country));
        }

        if ($status === 'active') {
            $query->whereHas('whitelistedBrokerUsage', function($q) {
                $q->where('last_seen_at', '>=', now()->subDays(30));
            });
        } elseif ($status === 'dormant') {
            $query->whereHas('whitelistedBrokerUsage', function($q) {
                $q->where('last_seen_at', '<', now()->subDays(30))
                  ->orWhereNull('last_seen_at');
            });
        }

        // Paginate
        $accounts = $query->with('whitelistedBrokerUsage')->paginate(50);

        // Get filter options
        $countries = TradingAccount::whereIn('id', function($q) use ($broker) {
            $q->select('trading_account_id')
              ->from('whitelisted_broker_usage')
              ->where('enterprise_broker_id', $broker->id);
        })
        ->whereNotNull('country_code')
        ->selectRaw('country_code, country_name')
        ->distinct()
        ->orderBy('country_name')
        ->get();

        $platforms = ['MT4', 'MT5'];

        return view('enterprise.accounts', compact(
            'broker',
            'accounts',
            'countries',
            'platforms',
            'platform',
            'country',
            'status'
        ));
    }

    /**
     * Show enterprise settings
     */
    public function settings()
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Get API keys for this broker
        $apiKeys = $broker->apiKeys()->orderBy('created_at', 'desc')->get();

        return view('enterprise.settings', compact('broker', 'apiKeys'));
    }

    /**
     * Update enterprise settings
     */
    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

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

    /**
     * Regenerate API key
     */
    public function regenerateApiKey()
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Revoke all existing keys
        $broker->apiKeys()->delete();

        // Generate new key
        $newKey = \App\Models\EnterpriseApiKey::create([
            'enterprise_broker_id' => $broker->id,
            'key' => \App\Models\EnterpriseApiKey::generateKey(),
            'name' => 'Primary API Key',
        ]);

        return back()->with('success', 'API key regenerated successfully')->with('new_api_key', $newKey->key);
    }

    /**
     * Manage enterprise admins
     */
    public function admins()
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Only admins can manage users
        if (!$admin->canManage()) {
            return redirect()->route('enterprise.dashboard')->with('error', 'Only administrators can manage users');
        }

        $admins = $broker->admins()->orderBy('created_at', 'desc')->get();

        return view('enterprise.admins', compact('broker', 'admins'));
    }

    /**
     * Store new enterprise admin
     */
    public function storeAdmin(Request $request)
    {
        $admin = Auth::guard('enterprise')->user();
        $broker = $admin->enterpriseBroker;

        if (!$broker || !$admin->canManage()) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:enterprise_admins,email',
            'role' => 'required|in:admin,viewer',
        ]);

        $newAdmin = \App\Models\EnterpriseAdmin::create([
            'enterprise_broker_id' => $broker->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make(\Str::random(32)), // Random password, will be reset via email
            'role' => $request->role,
            'is_active' => true,
        ]);

        // Generate password reset token
        $token = \Password::broker('enterprise_admins')->createToken($newAdmin);

        // Send welcome email with password setup link
        \Mail::to($newAdmin->email)->send(new \App\Mail\EnterpriseWelcomeMail($newAdmin, $token, $broker));

        return back()->with('success', 'User added successfully. They will receive an email to set their password.');
    }

    /**
     * Update enterprise admin
     */
    public function updateAdmin(Request $request, $adminId)
    {
        $currentAdmin = Auth::guard('enterprise')->user();
        $broker = $currentAdmin->enterpriseBroker;

        if (!$broker || !$currentAdmin->canManage()) {
            return back()->with('error', 'Unauthorized');
        }

        $targetAdmin = \App\Models\EnterpriseAdmin::where('enterprise_broker_id', $broker->id)
            ->findOrFail($adminId);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,viewer',
            'is_active' => 'boolean',
        ]);

        // Prevent deactivating yourself
        if ($targetAdmin->id === $currentAdmin->id && $request->is_active === false) {
            return back()->with('error', 'You cannot deactivate your own account');
        }

        $targetAdmin->update([
            'name' => $request->name,
            'role' => $request->role,
            'is_active' => $request->is_active ?? $targetAdmin->is_active,
        ]);

        return back()->with('success', 'User updated successfully');
    }

    /**
     * Delete enterprise admin
     */
    public function deleteAdmin($adminId)
    {
        $currentAdmin = Auth::guard('enterprise')->user();
        $broker = $currentAdmin->enterpriseBroker;

        if (!$broker || !$currentAdmin->canManage()) {
            return back()->with('error', 'Unauthorized');
        }

        $targetAdmin = \App\Models\EnterpriseAdmin::where('enterprise_broker_id', $broker->id)
            ->findOrFail($adminId);

        // Prevent deleting yourself
        if ($targetAdmin->id === $currentAdmin->id) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $targetAdmin->delete();

        return back()->with('success', 'User deleted successfully');
    }

}
