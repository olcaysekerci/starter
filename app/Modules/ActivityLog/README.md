# Activity Log Implementasyonu

Bu dokümantasyon, yeni modüllerinizde activity log'u nasıl kolayca implement edeceğinizi açıklar.

## 🚀 Hızlı Başlangıç

### 1. Model'inize Activity Log Ekleyin

```php
<?php

namespace App\Modules\YourModule\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\ActivityLog\Traits\LogsActivity;

class YourModel extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'status',
        // diğer alanlar...
    ];

    /**
     * Activity log ayarları
     */
    protected $loggableAttributes = [
        'name',
        'description', 
        'status',
        // Loglanacak alanları buraya ekleyin
    ];

    protected $displayName = 'Model Adı';

    /**
     * Activity log options override
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly($this->loggableAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('YourModel')
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
            'created' => 'Model Adı oluşturuldu',
            'updated' => 'Model Adı güncellendi',
            'deleted' => 'Model Adı silindi',
            'restored' => 'Model Adı geri yüklendi',
            default => "Model Adı üzerinde {$eventName} işlemi yapıldı",
        };
    }
}
```

### 2. Field Name Mapping Ekleyin

`app/Modules/ActivityLog/Models/Activity.php` dosyasında `getFormattedChangesAttribute()` metodundaki `$fieldNames` array'ine alan adlarınızı ekleyin:

```php
// Yeni Modül fields
'name' => 'Ad',
'description' => 'Açıklama',
'status' => 'Durum',
// diğer alanlar...
```

## 📋 Özellikler

### ✅ Otomatik Loglama
- Model oluşturma, güncelleme, silme işlemleri otomatik loglanır
- Sadece değişen alanlar loglanır
- Boş loglar kaydedilmez

### ✅ Türkçe Destek
- Event açıklamaları Türkçe
- Alan adları Türkçe
- Değişiklik detayları okunabilir

### ✅ Detaylı Bilgi
- Eski ve yeni değerler
- Hangi alanların değiştiği
- Önemli alanlar sarı etiketle işaretlenir

## 🔧 Özelleştirme

### Özel Event Açıklamaları

```php
protected function getDescriptionForEvent(string $eventName): string
{
    return match ($eventName) {
        'created' => 'Yeni kayıt eklendi',
        'updated' => 'Kayıt güncellendi',
        'deleted' => 'Kayıt silindi',
        'restored' => 'Kayıt geri yüklendi',
        default => "Kayıt üzerinde {$eventName} işlemi yapıldı",
    };
}
```

### Loglanacak Alanları Belirleme

```php
protected $loggableAttributes = [
    'name',           // Her zaman logla
    'description',    // Her zaman logla
    'status',         // Her zaman logla
    // Hassas alanları buraya eklemeyin (password, token vb.)
];
```

## 🚫 Dikkat Edilecekler

1. **Hassas Veriler**: Password, token gibi hassas alanları `$loggableAttributes`'a eklemeyin
2. **Performans**: Çok fazla alan loglamak performansı etkileyebilir
3. **Storage**: Loglar veritabanında yer kaplar, gereksiz alanları loglamayın

## 📊 Örnek Kullanım

```php
// Model oluşturma
$model = YourModel::create([
    'name' => 'Test Model',
    'description' => 'Test açıklama',
    'status' => 'active'
]);
// Otomatik olarak "Model Adı oluşturuldu" logu oluşur

// Model güncelleme
$model->update([
    'name' => 'Güncellenmiş Model',
    'status' => 'inactive'
]);
// Otomatik olarak "Model Adı güncellendi" logu oluşur
// Sadece değişen alanlar (name, status) loglanır
```

## 🔍 Log Görüntüleme

Activity logları panel üzerinden görüntülenebilir:
- `/panel/activity-logs` - Tüm loglar
- `/panel/activity-logs/{id}` - Detay sayfası

Her log kaydında:
- Hangi işlem yapıldığı
- Hangi alanların değiştiği
- Eski ve yeni değerler
- İşlemi yapan kullanıcı
- İşlem tarihi

görüntülenir. 