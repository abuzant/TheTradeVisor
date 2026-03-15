<?php

namespace App\Http\Middleware;

use App\Models\TradingAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = $request->route('account');

        if (!$account instanceof TradingAccount) {
            abort(404, 'Account not found');
        }

        $user = $request->user();

        if (!$user || $account->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this account');
        }

        return $next($request);
    }
}
