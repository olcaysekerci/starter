<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'key',
        'value',
        'type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'value' => 'json'
    ];

    /**
     * Cache key oluştur
     */
    public static function getCacheKey(string $category, string $key): string
    {
        return "settings.{$category}.{$key}";
    }

    /**
     * Cache'i temizle
     */
    public static function clearCache(string $category = null, string $key = null): void
    {
        if ($category && $key) {
            Cache::forget(self::getCacheKey($category, $key));
        } elseif ($category) {
            Cache::forget("settings.{$category}");
        } else {
            Cache::forget('settings');
        }
    }

    /**
     * Ayar değerini al (cache'den)
     */
    public static function get(string $category, string $key, $default = null)
    {
        $cacheKey = self::getCacheKey($category, $key);
        
        return Cache::remember($cacheKey, 3600, function () use ($category, $key, $default) {
            $setting = self::where('category', $category)
                          ->where('key', $key)
                          ->first();
            
            if (!$setting) {
                return $default;
            }

            return $setting->getTypedValue();
        });
    }

    /**
     * Ayar değerini kaydet
     */
    public static function set(string $category, string $key, $value, string $type = 'string', string $description = null): bool
    {
        $setting = self::updateOrCreate(
            ['category' => $category, 'key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description
            ]
        );

        // Cache'i temizle
        self::clearCache($category, $key);

        return (bool) $setting;
    }

    /**
     * Kategoriye ait tüm ayarları al
     */
    public static function getCategory(string $category): array
    {
        $cacheKey = "settings.{$category}";
        
        return Cache::remember($cacheKey, 3600, function () use ($category) {
            return self::where('category', $category)
                      ->get()
                      ->mapWithKeys(function ($setting) {
                          return [$setting->key => $setting->getTypedValue()];
                      })
                      ->toArray();
        });
    }

    /**
     * Tip'e göre değeri döndür
     */
    public function getTypedValue()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'json':
                return is_string($this->value) ? json_decode($this->value, true) : $this->value;
            default:
                return $this->value;
        }
    }

    /**
     * Değeri tip'e göre kaydet
     */
    public function setTypedValue($value): void
    {
        switch ($this->type) {
            case 'boolean':
                $this->value = (bool) $value;
                break;
            case 'integer':
                $this->value = (int) $value;
                break;
            case 'json':
                $this->value = is_array($value) ? json_encode($value) : $value;
                break;
            default:
                $this->value = (string) $value;
        }
    }
} 