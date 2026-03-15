<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\User;
use App\Models\SymbolMapping;
use App\Services\CurrencyService;
use Carbon\Carbon;

class TradesController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $search    = $request->get('search');
        $userId    = $request->get('user_id');
        $accountId = $request->get('account');
        $symbol    = $request->get('symbol');
        $type      = $request->get('type', 'all');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');
        $perPage   = (int) $request->get('per_page', 25);

        $sortableColumns = [
            'time','symbol','type','volume','price','profit','commission','swap',
        ];

        $query = Deal::with(['tradingAccount.user'])
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('symbol', 'like', "%{$search}%")
                  ->orWhere('ticket', 'like', "%{$search}%")
                  ->orWhereHas('tradingAccount.user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($userId) {
            $query->whereHas('tradingAccount', fn ($q) => $q->where('user_id', $userId));
        }

        if ($accountId) {
            $query->where('trading_account_id', $accountId);
        }

        if ($symbol) {
            $rawSymbols = SymbolMapping::where('normalized_symbol', $symbol)->pluck('raw_symbol');
            $rawSymbols->isNotEmpty()
                ? $query->whereIn('symbol', $rawSymbols)
                : $query->where('symbol', $symbol);
        }

        if ($type && $type !== 'all') {
            switch ($type) {
                case 'buy':    $query->where('type', 'like', 'buy%'); break;
                case 'sell':   $query->where('type', 'like', 'sell%'); break;
                case 'trades': $query->where(fn ($q) => $q->where('type','like','buy%')->orWhere('type','like','sell%')); break;
                case 'cashier':$query->whereIn('type', ['balance','credit']); break;
                case 'fees':   $query->where('type', 'commission'); break;
                case 'swaps':  $query->where('type', 'swap'); break;
            }
        }

        if ($dateFrom) { $query->where('time', '>=', $dateFrom.' 00:00:00'); }
        if ($dateTo)   { $query->where('time', '<=', $dateTo.' 23:59:59'); }

        // clone BEFORE sorting/pagination
        $totalsQuery = clone $query;

        // sorting via trait (defaults to time desc)
        $query = $this->applySorting($query, $request, $sortableColumns, 'time', 'desc');

        $deals = $query->paginate($perPage)->appends($request->query());
        
        // OPTIMIZED: Batch-load open positions for 'in' deals to avoid N+1 queries
        $inDeals = $deals->where('entry', 'in')->filter(fn($d) => $d->position_id);
        $positionIds = $inDeals->pluck('position_id')->unique()->values()->toArray();
        $accountIds = $inDeals->pluck('trading_account_id')->unique()->values()->toArray();

        // Load all matching open positions in 2 queries (by position_identifier and ticket)
        $openPositionsByIdentifier = !empty($positionIds) ? \App\Models\Position::whereIn('trading_account_id', $accountIds)
            ->whereIn('position_identifier', $positionIds)
            ->where('is_open', true)
            ->get()
            ->keyBy(fn($p) => $p->trading_account_id . '_' . $p->position_identifier) : collect();

        $openPositionsByTicket = !empty($positionIds) ? \App\Models\Position::whereIn('trading_account_id', $accountIds)
            ->whereIn('ticket', $positionIds)
            ->where('is_open', true)
            ->get()
            ->keyBy(fn($p) => $p->trading_account_id . '_' . $p->ticket) : collect();

        // Group deals by position_id for better UX
        $groupedDeals = [];
        foreach ($deals as $deal) {
            $posId = $deal->position_id ?? 'no_position_' . $deal->ticket;
            
            if (!isset($groupedDeals[$posId])) {
                $groupedDeals[$posId] = [
                    'position_id' => $deal->position_id,
                    'in_deal' => null,
                    'out_deal' => null,
                    'total_profit' => 0,
                    'is_open' => false,
                ];
            }
            
            if ($deal->entry === 'in') {
                $groupedDeals[$posId]['in_deal'] = $deal;
                $groupedDeals[$posId]['is_open'] = true;
                
                // Look up open position from pre-loaded collections (zero queries)
                $lookupKey = $deal->trading_account_id . '_' . $deal->position_id;
                $position = $openPositionsByIdentifier->get($lookupKey)
                    ?? $openPositionsByTicket->get($lookupKey);
                
                if ($position) {
                    $deal->openPosition = $position;
                    $groupedDeals[$posId]['total_profit'] = $position->profit;
                }
            } elseif ($deal->entry === 'out') {
                $groupedDeals[$posId]['out_deal'] = $deal;
                $groupedDeals[$posId]['is_open'] = false;
                $groupedDeals[$posId]['total_profit'] = $deal->profit;
            }
        }
        
        // Convert to collection for view
        $groupedDeals = collect($groupedDeals)->values();

        $symbols = SymbolMapping::select('normalized_symbol')
            ->distinct()->orderBy('normalized_symbol')->pluck('normalized_symbol');

        if ($symbols->isEmpty()) {
            $symbols = Deal::select('symbol')
                ->whereNotNull('symbol')->where('symbol','!=','')
                ->distinct()->orderBy('symbol')->pluck('symbol');
        }

        $users = User::orderBy('name')->limit(1000)->get();

        // Calculate totals with currency conversion to USD (aggregate at DB level per currency)
        $currencyService = app(CurrencyService::class);
        $currencyTotals = (clone $totalsQuery)
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->selectRaw("COALESCE(trading_accounts.account_currency, 'USD') as currency, SUM(deals.profit) as total_profit, SUM(deals.commission) as total_commission, SUM(deals.fee) as total_fee, SUM(deals.swap) as total_swap")
            ->groupByRaw("COALESCE(trading_accounts.account_currency, 'USD')")
            ->get();

        $totalProfitUSD = 0;
        $totalCommissionUSD = 0;
        $totalFeesUSD = 0;
        $totalSwapUSD = 0;

        foreach ($currencyTotals as $row) {
            $currency = $row->currency ?: 'USD';
            $totalProfitUSD += $currencyService->convert((float) ($row->total_profit ?? 0), $currency, 'USD');
            $totalCommissionUSD += $currencyService->convert((float) ($row->total_commission ?? 0), $currency, 'USD');
            $totalFeesUSD += $currencyService->convert((float) ($row->total_fee ?? 0), $currency, 'USD');
            $totalSwapUSD += $currencyService->convert((float) ($row->total_swap ?? 0), $currency, 'USD');
        }

        $totals = [
            'totalProfit'     => $totalProfitUSD,
            'totalCommission' => $totalCommissionUSD,
            'totalFees'       => $totalFeesUSD,
            'totalSwap'       => $totalSwapUSD,
            'totalVolume'     => (float) $totalsQuery->sum('volume'),
            'tradeCount'      => (int)   $totalsQuery->count(),
        ];

        $stats = [
            'total'        => Deal::whereNotNull('symbol')->where('symbol','!=','')->count(),
            'today'        => Deal::whereNotNull('symbol')->where('symbol','!=','')->whereDate('time', Carbon::today())->count(),
            'week'         => Deal::whereNotNull('symbol')->where('symbol','!=','')->whereBetween('time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        $sortBy        = $request->get('sort_by', 'time');
        $sortDirection = $request->get('sort_direction', 'desc');

        return view('admin.trades.index', compact(
            'deals','groupedDeals','symbols','users','search','userId','accountId','symbol','type',
            'dateFrom','dateTo','perPage','totals','stats','sortBy','sortDirection'
        ));
    }
}

