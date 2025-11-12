<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function landing()
    {
        // Get comprehensive statistics (cached for 5 minutes)
        $stats = Cache::remember('landing_stats_comprehensive', 300, function() {
            $thirtyDaysAgo = now()->subDays(30);
            
            return [
                // Core Metrics
                'total_traders' => TradingAccount::count(),
                'active_traders' => TradingAccount::where('is_active', true)->count(),
                'total_trades_30d' => Deal::where('time', '>=', $thirtyDaysAgo)->count(),
                'total_volume_30d' => Deal::where('time', '>=', $thirtyDaysAgo)->sum('volume'),
                
                // Geographic
                'countries' => TradingAccount::whereNotNull('country_code')
                    ->distinct('country_code')->count('country_code'),
                'top_country' => TradingAccount::select('country_code', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('country_code')
                    ->groupBy('country_code')
                    ->orderBy('count', 'desc')
                    ->first(),
                
                // Trading Activity
                'avg_trades_per_day' => round(Deal::where('time', '>=', $thirtyDaysAgo)->count() / 30, 0),
                'total_symbols' => Deal::where('time', '>=', $thirtyDaysAgo)
                    ->distinct('symbol')->count('symbol'),
                'most_traded_symbol' => $this->getMostTradedSymbol($thirtyDaysAgo),
                
                // Performance
                'total_profit_30d' => Deal::where('time', '>=', $thirtyDaysAgo)->sum('profit'),
                'winning_trades' => Deal::where('time', '>=', $thirtyDaysAgo)->where('profit', '>', 0)->count(),
                'losing_trades' => Deal::where('time', '>=', $thirtyDaysAgo)->where('profit', '<', 0)->count(),
                'win_rate' => $this->calculateWinRate($thirtyDaysAgo),
                
                // Brokers & Platforms
                'total_brokers' => TradingAccount::distinct('broker_name')->count('broker_name'),
                'mt4_accounts' => TradingAccount::where('platform_type', 'MT4')->count(),
                'mt5_accounts' => TradingAccount::where('platform_type', 'MT5')->count(),
                
                // Advanced Metrics
                'avg_position_size' => round(Deal::where('time', '>=', $thirtyDaysAgo)->avg('volume'), 2),
                'largest_trade' => Deal::where('time', '>=', $thirtyDaysAgo)->max('profit'),
                'data_points' => Deal::count() + TradingAccount::count(),
            ];
        });

        return view('public.landing', compact('stats'));
    }
    
    private function calculateWinRate($since)
    {
        $total = Deal::where('time', '>=', $since)->count();
        if ($total == 0) return 0;
        
        $winning = Deal::where('time', '>=', $since)->where('profit', '>', 0)->count();
        return round(($winning / $total) * 100, 1);
    }
    
    private function getMostTradedSymbol($since)
    {
        $result = Deal::select('symbol', DB::raw('COUNT(*) as count'))
            ->where('time', '>=', $since)
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->orderBy('count', 'desc')
            ->first();
            
        if ($result) {
            $result->symbol = \App\Models\SymbolMapping::normalize($result->symbol);
        }
        
        return $result;
    }

    public function features()
    {
        return view('public.features');
    }
    
    public function screenshots()
    {
        return view('public.screenshots');
    }
    
    public function pricing()
    {
        return view('public.pricing');
    }

    public function about()
    {
        return view('public.about');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email or store in database
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
    
    public function docs()
    {
        return view('public.docs');
    }
    
    public function apiDocs()
    {
        return view('public.api-docs');
    }
}
