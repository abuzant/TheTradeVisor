<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UninstallFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'experience_rating',
        'would_return',
        'email',
        'comments',
        'ip_address',
        'user_agent',
        'referer',
        'utm_parameters',
        'submitted_at'
    ];

    protected $casts = [
        'utm_parameters' => 'array',
        'submitted_at' => 'datetime',
    ];
}
