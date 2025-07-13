<?php

namespace App\Modules\User\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Modules\ActivityLog\Traits\LogsActivity;

class Role extends SpatieRole
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_active'
    ];

    protected $guarded = [];

    /**
     * Activity log ayarları
     */
    protected $loggableAttributes = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $displayName = 'Rol';

    /**
     * Activity log options override
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly($this->loggableAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Role')
            ->setDescriptionForEvent(function (string $eventName) {
                return $this->getDescriptionForEvent($eventName);
            });
    }

    /**
     * Event açıklamalarını belirle
     */
    protected function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Rol oluşturuldu',
            'updated' => 'Rol güncellendi',
            'deleted' => 'Rol silindi',
            'restored' => 'Rol geri yüklendi',
            default => "Rol üzerinde {$eventName} işlemi yapıldı",
        };
    }

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

    /**
     * Özel alan adlarını belirle
     */
    protected function getAttributeNames(): array
    {
        return [
            'name' => 'Rol Adı',
            'display_name' => 'Görünen Ad',
            'description' => 'Açıklama',
            'is_active' => 'Aktif Durumu',
        ];
    }
} 