<?php

namespace App\Modules\ActivityLog\Templates;

/**
 * Yeni Modül Activity Log Template
 * 
 * Bu template'i kullanarak yeni modüllerinizde activity log'u kolayca implement edebilirsiniz.
 * 
 * Kullanım:
 * 1. Model'inizde LogsActivity trait'ini kullanın
 * 2. Aşağıdaki kodları model'inize ekleyin
 * 3. Field name mapping'i Activity.php'de genişletin
 */

class ModelTemplate
{
    /**
     * Model'inize eklenecek kodlar:
     * 
     * use App\Modules\ActivityLog\Traits\LogsActivity;
     * 
     * protected $loggableAttributes = [
     *     'field1',
     *     'field2',
     *     'field3',
     * ];
     * 
     * protected $displayName = 'Model Adı';
     * 
     * public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
     * {
     *     return \Spatie\Activitylog\LogOptions::defaults()
     *         ->logOnly($this->loggableAttributes)
     *         ->logOnlyDirty()
     *         ->dontSubmitEmptyLogs()
     *         ->useLogName('ModelName')
     *         ->setDescriptionForEvent(function (string $eventName) {
     *             return $this->getDescriptionForEvent($eventName);
     *         });
     * }
     * 
     * protected function getDescriptionForEvent(string $eventName): string
     * {
     *     return match ($eventName) {
     *         'created' => 'Model Adı oluşturuldu',
     *         'updated' => 'Model Adı güncellendi',
     *         'deleted' => 'Model Adı silindi',
     *         'restored' => 'Model Adı geri yüklendi',
     *         default => "Model Adı üzerinde {$eventName} işlemi yapıldı",
     *     };
     * }
     */
} 