<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EnterpriseApiKey extends Model
{
    protected $fillable = [
        'enterprise_broker_id',
        'key',
        'name',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * Relationship: Belongs to EnterpriseBroker
     */
    public function enterpriseBroker()
    {
        return $this->belongsTo(EnterpriseBroker::class);
    }

    /**
     * Generate a new enterprise API key with 'ent_' prefix
     */
    public static function generateKey()
    {
        return 'ent_' . Str::random(64);
    }

    /**
     * Update last used timestamp
     */
    public function markAsUsed()
    {
        $this->last_used_at = now();
        $this->save();
    }

    /**
     * Check if key is valid (not revoked, broker is active)
     */
    public function isValid()
    {
        return $this->enterpriseBroker && $this->enterpriseBroker->isCurrentlyActive();
    }
}
