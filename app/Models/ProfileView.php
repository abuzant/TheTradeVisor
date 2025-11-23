<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'public_profile_account_id',
        'ip_address',
        'user_agent',
        'referer',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Relationships
    public function publicProfileAccount(): BelongsTo
    {
        return $this->belongsTo(PublicProfileAccount::class);
    }
}
