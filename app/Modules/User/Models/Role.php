<?php

namespace App\Modules\User\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_active'
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($role) {
            if (empty($role->guard_name)) {
                $role->guard_name = 'web';
            }
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Aktif rolleri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sistem rolleri hariç tut
     */
    public function scopeExcludeSystem($query)
    {
        return $query->whereNotIn('name', ['super-admin', 'admin']);
    }

    /**
     * Role sahip kullanıcı sayısını getir
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Role ait yetkileri getir
     */
    public function getPermissionsListAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }
} 