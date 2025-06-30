<?php

namespace App\Modules\User\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'module',
        'is_active'
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($permission) {
            if (empty($permission->guard_name)) {
                $permission->guard_name = 'web';
            }
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Aktif yetkileri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Modüle göre yetkileri getir
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Sistem yetkileri hariç tut
     */
    public function scopeExcludeSystem($query)
    {
        return $query->whereNotIn('name', ['super-admin', 'admin']);
    }

    /**
     * Yetkiye sahip kullanıcı sayısını getir
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Yetkiye sahip rol sayısını getir
     */
    public function getRolesCountAttribute()
    {
        return $this->roles()->count();
    }
} 