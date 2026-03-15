<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BackfillSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'trading_account_id',
        'trigger_type',
        'priority',
        'status',
        'gap_start_time',
        'gap_end_time',
        'estimated_missing_snapshots',
        'total_files_to_process',
        'files_processed',
        'snapshots_created',
        'errors_count',
        'started_at',
        'completed_at',
        'duration_seconds',
        'completion_percentage',
        'error_message',
        'failed_files',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'gap_start_time' => 'datetime',
        'gap_end_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_files' => 'array',
        'metadata' => 'array',
        'completion_percentage' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_id)) {
                $session->session_id = Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }

    /**
     * Status helpers
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRunning()
    {
        return $this->status === 'running';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Start the session
     */
    public function start()
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Complete the session
     */
    public function complete($snapshotsCreated = 0)
    {
        $duration = 0;
        if ($this->started_at) {
            $duration = max(0, now()->diffInSeconds($this->started_at));
        }
        
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => $duration,
            'completion_percentage' => 100.00,
            'snapshots_created' => $this->snapshots_created + $snapshotsCreated,
        ]);
    }

    /**
     * Fail the session
     */
    public function fail($errorMessage)
    {
        $duration = 0;
        if ($this->started_at) {
            $duration = max(0, now()->diffInSeconds($this->started_at));
        }
        
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_seconds' => $duration,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress($filesProcessed, $snapshotsCreated = 0, $errorsCount = 0)
    {
        $completionPercentage = $this->total_files_to_process > 0 
            ? round(($filesProcessed / $this->total_files_to_process) * 100, 2)
            : 0;

        $this->update([
            'files_processed' => $filesProcessed,
            'snapshots_created' => $this->snapshots_created + $snapshotsCreated,
            'errors_count' => $this->errors_count + $errorsCount,
            'completion_percentage' => $completionPercentage,
        ]);
    }

    /**
     * Add failed file
     */
    public function addFailedFile($filename, $error = null)
    {
        $failedFiles = $this->failed_files ?? [];
        $failedFiles[] = [
            'filename' => $filename,
            'error' => $error,
            'failed_at' => now()->toDateTimeString(),
        ];
        
        $this->update([
            'failed_files' => $failedFiles,
            'errors_count' => $this->errors_count + 1,
        ]);
    }

    /**
     * Get duration as human readable
     */
    public function getHumanDuration()
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Get priority color for display
     */
    public function getPriorityColor()
    {
        return [
            'low' => 'gray',
            'normal' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
        ][$this->priority] ?? 'gray';
    }

    /**
     * Get status color for display
     */
    public function getStatusColor()
    {
        return [
            'pending' => 'yellow',
            'running' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
        ][$this->status] ?? 'gray';
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'running']);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'critical', 'high', 'normal', 'low')");
    }
}
