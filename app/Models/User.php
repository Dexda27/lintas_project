<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'region',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected $attributes = [
        'is_active' => true,
    ];

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdminRegion()
    {
        return $this->role === 'admin_region';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function canManageUsers()
    {
        return $this->isSuperAdmin();
    }

    public function canAccessRegion($region)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isAdminRegion()) {
            return $this->region === $region;
        }

        return false;
    }

    public function getFullRoleNameAttribute()
    {
        return match ($this->role) {
            'super_admin' => 'Super Administrator',
            'admin_region' => 'Regional Administrator',
            'user' => 'User',
            default => 'Unknown Role'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }
}