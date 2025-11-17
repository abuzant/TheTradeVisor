<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigestSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_account_id',
        'frequency',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }
}
