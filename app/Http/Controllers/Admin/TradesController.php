<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\User;
use App\Models\SymbolMapping;
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

        $symbols = SymbolMapping::select('normalized_symbol')
            ->distinct()->orderBy('normalized_symbol')->pluck('normalized_symbol');

        if ($symbols->isEmpty()) {
            $symbols = Deal::select('symbol')
                ->whereNotNull('symbol')->where('symbol','!=','')
                ->distinct()->orderBy('symbol')->pluck('symbol');
        }

        $users = User::orderBy('name')->limit(1000)->get();

        $totals = [
            'totalProfit'     => (float) $totalsQuery->sum('profit'),
            'totalCommission' => (float) $totalsQuery->sum('commission'),
            'totalFees'       => (float) $totalsQuery->sum('fee'),
            'totalSwap'       => (float) $totalsQuery->sum('swap'),
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
            'deals','symbols','users','search','userId','accountId','symbol','type',
            'dateFrom','dateTo','perPage','totals','stats','sortBy','sortDirection'
        ));
    }
}

