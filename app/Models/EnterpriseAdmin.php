<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EnterpriseAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'enterprise_broker_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the enterprise broker this admin belongs to
     */
    public function enterpriseBroker()
    {
        return $this->belongsTo(EnterpriseBroker::class);
    }

    /**
     * Check if admin has admin role (not just viewer)
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if admin is a viewer
     */
    public function isViewer()
    {
        return $this->role === 'viewer';
    }

    /**
     * Check if admin can perform administrative actions
     */
    public function canManage()
    {
        return $this->is_active && $this->isAdmin();
    }
}
