<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TradingAccount;

class AccountSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_account_id',
        'balance',
        'equity',
        'margin',
        'free_margin',
        'margin_level',
        'profit',
        'snapshot_time',
        'is_historical',
        'source',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'margin' => 'decimal:2',
        'free_margin' => 'decimal:2',
        'margin_level' => 'decimal:2',
        'profit' => 'decimal:2',
        'snapshot_time' => 'datetime',
        'is_historical' => 'boolean',
    ];

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
