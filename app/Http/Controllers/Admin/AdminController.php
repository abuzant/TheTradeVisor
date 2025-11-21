<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Deal;
use App\Models\EnterpriseBroker;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        // Get unique broker names from trading accounts
        $knownBrokers = TradingAccount::distinct('broker_name')
            ->whereNotNull('broker_name')
            ->count('broker_name');
        
        // Get enterprise brokers count
        $enterpriseBrokers = EnterpriseBroker::where('is_active', true)->count();
        
        // Get next enterprise broker expiry
        $nextExpiry = EnterpriseBroker::where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->orderBy('subscription_ends_at', 'asc')
            ->first();
        
        // Get active terminals (sent data within last hour)
        $activeTerminals = TradingAccount::where('last_sync_at', '>=', now()->subHour())
            ->count();

        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_accounts' => TradingAccount::count(),
            'active_accounts' => TradingAccount::where('is_active', true)->count(),
            'total_positions' => Position::where('is_open', true)->count(),
            'total_trades_today' => Deal::whereDate('time', today())->count(),
            'total_volume_today' => Deal::whereDate('time', today())->sum('volume'),
            'known_brokers' => $knownBrokers,
            'enterprise_brokers' => $enterpriseBrokers,
            'active_terminals' => $activeTerminals,
        ];

        // Get list of enterprise broker names for star indicator
        $enterpriseBrokerNames = EnterpriseBroker::where('is_active', true)
            ->pluck('official_broker_name')
            ->toArray();

        // Sortable columns for users
        $userSortableColumns = ['name', 'email', 'created_at'];
        $usersQuery = User::with(['tradingAccounts' => function($query) {
            $query->select('user_id', 'broker_name', 'id');
        }]);

        if ($request->has('users_sort_by')) {
            $usersQuery = $this->applySorting($usersQuery, $request, $userSortableColumns, 'created_at', 'desc', 'users_');
        } else {
            $usersQuery->latest();
        }

        $recentUsers = $usersQuery->paginate(10, ['*'], 'users_page');

        // Sortable columns for accounts
        $accountSortableColumns = ['broker_name', 'account_number', 'balance', 'equity', 'last_sync_at'];
        $accountsQuery = TradingAccount::with('user');

        if ($request->has('accounts_sort_by')) {
            $accountsQuery = $this->applySorting($accountsQuery, $request, $accountSortableColumns, 'last_sync_at', 'desc', 'accounts_');
        } else {
            $accountsQuery->latest('last_sync_at');
        }

        $recentAccounts = $accountsQuery->paginate(10, ['*'], 'accounts_page');

        // Pass sorting parameters to view
        $usersSortBy = $request->get('users_sort_by', 'created_at');
        $usersSortDirection = $request->get('users_sort_direction', 'desc');
        $accountsSortBy = $request->get('accounts_sort_by', 'last_sync_at');
        $accountsSortDirection = $request->get('accounts_sort_direction', 'desc');

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentAccounts',
            'usersSortBy',
            'usersSortDirection',
            'accountsSortBy',
            'accountsSortDirection',
            'nextExpiry',
            'enterpriseBrokerNames'
        ));
    }


}
