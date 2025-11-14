<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\Position;
use App\Models\Order;

$accountId = 114;
$userId = 26;

$acc = TradingAccount::where('user_id', $userId)->where('id', $accountId)->first();

if (!$acc) {
    echo "Account {$accountId} not found for user {$userId}".PHP_EOL;
    exit(0);
}

echo "Deleting trading data for user {$userId}, account ID {$accountId}".PHP_EOL;

echo 'Deals: '.Deal::where('trading_account_id', $accountId)->count().PHP_EOL;
echo 'Positions: '.Position::where('trading_account_id', $accountId)->count().PHP_EOL;
echo 'Orders: '.Order::where('trading_account_id', $accountId)->count().PHP_EOL;

echo 'Deleting records...'.PHP_EOL;
Deal::where('trading_account_id', $accountId)->delete();
Position::where('trading_account_id', $accountId)->delete();
Order::where('trading_account_id', $accountId)->delete();
$acc->delete();

echo 'Remaining accounts for user 26: '.TradingAccount::where('user_id', $userId)->count().PHP_EOL;
