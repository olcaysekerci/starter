<?php

namespace App\Modules\ActivityLog\DTOs;

use Illuminate\Support\Collection;

class ActivityLogDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $log_name = null,
        public readonly string $description,
        public readonly ?string $subject_type = null,
        public readonly ?int $subject_id = null,
        public readonly ?string $event = null,
        public readonly ?int $causer_id = null,
        public readonly ?string $causer_type = null,
        public readonly ?array $properties = null,
        public readonly ?string $batch_uuid = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $user_name = null,
        public readonly ?string $model_name = null,
        public readonly ?string $formatted_description = null,
        public readonly ?string $ip_address = null,
        public readonly ?string $user_agent = null,
        public readonly bool $has_changes = false,
        public readonly array $formatted_changes = [],
        public readonly bool $is_password_change = false,
        public readonly bool $is_email_change = false,
        public readonly bool $is_admin_action = false,
        public readonly ?array $deleted_info = null,
        public readonly ?Collection $causer = null,
        public readonly ?Collection $subject = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            log_name: $data['log_name'] ?? null,
            description: $data['description'],
            subject_type: $data['subject_type'] ?? null,
            subject_id: $data['subject_id'] ?? null,
            event: $data['event'] ?? null,
            causer_id: $data['causer_id'] ?? null,
            causer_type: $data['causer_type'] ?? null,
            properties: $data['properties'] ?? null,
            batch_uuid: $data['batch_uuid'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            user_name: $data['user_name'] ?? null,
            model_name: $data['model_name'] ?? null,
            formatted_description: $data['formatted_description'] ?? null,
            ip_address: $data['ip_address'] ?? null,
            user_agent: $data['user_agent'] ?? null,
            has_changes: $data['has_changes'] ?? false,
            formatted_changes: $data['formatted_changes'] ?? [],
            is_password_change: $data['is_password_change'] ?? false,
            is_email_change: $data['is_email_change'] ?? false,
            is_admin_action: $data['is_admin_action'] ?? false,
            deleted_info: $data['deleted_info'] ?? null,
            causer: isset($data['causer']) ? collect($data['causer']) : null,
            subject: isset($data['subject']) ? collect($data['subject']) : null,
        );
    }

    public static function fromModel($activity): self
    {
        return new self(
            id: $activity->id,
            log_name: $activity->log_name,
            description: $activity->description,
            subject_type: $activity->subject_type,
            subject_id: $activity->subject_id,
            event: $activity->event,
            causer_id: $activity->causer_id,
            causer_type: $activity->causer_type,
            properties: $activity->properties ? $activity->properties->toArray() : null,
            batch_uuid: $activity->batch_uuid,
            created_at: $activity->created_at?->toISOString(),
            updated_at: $activity->updated_at?->toISOString(),
            user_name: $activity->user_name ?? null,
            model_name: $activity->model_name ?? null,
            formatted_description: $activity->formatted_description ?? null,
            ip_address: $activity->ip_address ?? null,
            user_agent: $activity->user_agent ?? null,
            has_changes: $activity->has_changes ?? false,
            formatted_changes: $activity->formatted_changes ?? [],
            is_password_change: $activity->is_password_change ?? false,
            is_email_change: $activity->is_email_change ?? false,
            is_admin_action: $activity->is_admin_action ?? false,
            deleted_info: $activity->deleted_info ?? null,
            causer: $activity->causer ?? null,
            subject: $activity->subject ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'event' => $this->event,
            'causer_id' => $this->causer_id,
            'causer_type' => $this->causer_type,
            'properties' => $this->properties,
            'batch_uuid' => $this->batch_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_name' => $this->user_name,
            'model_name' => $this->model_name,
            'formatted_description' => $this->formatted_description,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'has_changes' => $this->has_changes,
            'formatted_changes' => $this->formatted_changes,
            'is_password_change' => $this->is_password_change,
            'is_email_change' => $this->is_email_change,
            'is_admin_action' => $this->is_admin_action,
            'deleted_info' => $this->deleted_info,
            'causer' => $this->causer?->toArray(),
            'subject' => $this->subject?->toArray(),
        ];
    }

    public function isCreated(): bool
    {
        return $this->event === 'created';
    }

    public function isUpdated(): bool
    {
        return $this->event === 'updated';
    }

    public function isDeleted(): bool
    {
        return $this->event === 'deleted';
    }

    public function isLogin(): bool
    {
        return $this->event === 'login';
    }

    public function isLogout(): bool
    {
        return $this->event === 'logout';
    }

    public function hasUser(): bool
    {
        return !is_null($this->causer_id);
    }

    public function hasSubject(): bool
    {
        return !is_null($this->subject_type) && !is_null($this->subject_id);
    }

    public function getChangeSummary(): string
    {
        if (!$this->has_changes) {
            return $this->description ?? 'Değişiklik yok';
        }

        $changeCount = count($this->formatted_changes);
        
        if ($changeCount === 1) {
            $fieldName = $this->formatted_changes[0]['field_name'] ?? $this->formatted_changes[0]['field'];
            return "{$fieldName} alanını değiştirdi";
        }

        return "{$changeCount} alanı değiştirdi";
    }
} 