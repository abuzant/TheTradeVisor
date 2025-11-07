<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    // We only use updated_at, not created_at
    public $timestamps = false;
    
    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'updated_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'updated_at' => 'datetime',
    ];
}
