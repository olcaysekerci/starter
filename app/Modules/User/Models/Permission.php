<?php

namespace App\Modules\User\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Modules\ActivityLog\Traits\LogsActivity;

class Permission extends SpatiePermission
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'module',
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
        'module',
        'is_active',
    ];

    protected $displayName = 'Yetki';

    /**
     * Activity log options override
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly($this->loggableAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Permission')
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
            'created' => 'Yetki oluşturuldu',
            'updated' => 'Yetki güncellendi',
            'deleted' => 'Yetki silindi',
            'restored' => 'Yetki geri yüklendi',
            default => "Yetki üzerinde {$eventName} işlemi yapıldı",
        };
    }

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

    /**
     * Özel alan adlarını belirle
     */
    protected function getAttributeNames(): array
    {
        return [
            'name' => 'Yetki Adı',
            'display_name' => 'Görünen Ad',
            'description' => 'Açıklama',
            'module' => 'Modül',
            'is_active' => 'Aktif Durumu',
        ];
    }
} 