<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryUploadProgress extends Model
{
    protected $table = 'history_upload_progress';

    protected $fillable = [
        'trading_account_id',
        'last_day_uploaded',
        'days_processed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'last_day_uploaded' => 'date',
        'days_processed' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the trading account this progress belongs to
     */
    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    /**
     * Check if history upload is complete
     */
    public function isComplete(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark upload as complete
     */
    public function markComplete(): void
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }
}
