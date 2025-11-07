<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_accounts' => TradingAccount::count(),
            'active_accounts' => TradingAccount::where('is_active', true)->count(),
            'total_positions' => Position::where('is_open', true)->count(),
            'total_trades_today' => Deal::whereDate('time', today())->count(),
            'total_volume_today' => Deal::whereDate('time', today())->sum('volume'),
        ];

        // Sortable columns for users
        $userSortableColumns = ['name', 'email', 'created_at'];
        $usersQuery = User::query();

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
            'accountsSortDirection'
        ));
    }


}
