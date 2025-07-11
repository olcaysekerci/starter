<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getLogAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the attributes that should be logged
     */
    protected function getLogAttributes(): array
    {
        return property_exists($this, 'logAttributes') ? $this->logAttributes : ['*'];
    }

    /**
     * Get the log name for this model
     */
    protected function getLogName(): string
    {
        return property_exists($this, 'logName') ? $this->logName : class_basename($this);
    }

    /**
     * Get description for activity log event
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = class_basename($this);
        return "{$modelName} {$eventName}";
    }
}