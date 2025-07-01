<?php

namespace App\Modules\Settings\DTOs;

class SettingsDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $category,
        public readonly string $key,
        public readonly mixed $value,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $formatted_value = null,
        public readonly ?string $type_label = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            category: $data['category'],
            key: $data['key'],
            value: $data['value'],
            type: $data['type'],
            description: $data['description'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            formatted_value: $data['formatted_value'] ?? null,
            type_label: $data['type_label'] ?? null,
        );
    }

    public static function fromModel($setting): self
    {
        return new self(
            id: $setting->id,
            category: $setting->category,
            key: $setting->key,
            value: $setting->value,
            type: $setting->type,
            description: $setting->description,
            created_at: $setting->created_at?->toISOString(),
            updated_at: $setting->updated_at?->toISOString(),
            formatted_value: $setting->formatted_value ?? null,
            type_label: $setting->type_label ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'key' => $this->key,
            'value' => $this->value,
            'type' => $this->type,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'formatted_value' => $this->formatted_value,
            'type_label' => $this->type_label,
        ];
    }

    public function isAppSetting(): bool
    {
        return $this->category === 'app';
    }

    public function isMailSetting(): bool
    {
        return $this->category === 'mail';
    }

    public function isBoolean(): bool
    {
        return $this->type === 'boolean';
    }

    public function isInteger(): bool
    {
        return $this->type === 'integer';
    }

    public function isString(): bool
    {
        return $this->type === 'string';
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'string' => 'Metin',
            'integer' => 'Sayı',
            'boolean' => 'Evet/Hayır',
            'array' => 'Dizi',
            'json' => 'JSON',
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    public function getCategoryLabel(): string
    {
        $labels = [
            'app' => 'Uygulama',
            'mail' => 'Mail',
            'system' => 'Sistem',
            'security' => 'Güvenlik',
        ];

        return $labels[$this->category] ?? ucfirst($this->category);
    }

    public function getFormattedValue(): string
    {
        if ($this->isBoolean()) {
            return $this->value ? 'Evet' : 'Hayır';
        }

        if ($this->isInteger()) {
            return number_format($this->value);
        }

        return (string) $this->value;
    }

    public function isSensitive(): bool
    {
        $sensitiveKeys = [
            'password',
            'secret',
            'key',
            'token',
            'api_key',
        ];

        return in_array(strtolower($this->key), $sensitiveKeys);
    }

    public function getDisplayValue(): string
    {
        if ($this->isSensitive()) {
            return str_repeat('*', 8);
        }

        return $this->getFormattedValue();
    }
} 