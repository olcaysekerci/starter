<?php

namespace App\Modules\Dashboard\DTOs;

class DashboardDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly array $data,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?array $statistics = null,
        public readonly ?array $widgets = null,
        public readonly ?array $charts = null,
        public readonly ?array $user_preferences = null,
        public readonly ?string $layout = null,
        public readonly ?bool $is_active = null,
        public readonly ?string $theme = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'],
            data: $data['data'] ?? [],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            statistics: $data['statistics'] ?? null,
            widgets: $data['widgets'] ?? null,
            charts: $data['charts'] ?? null,
            user_preferences: $data['user_preferences'] ?? null,
            layout: $data['layout'] ?? null,
            is_active: $data['is_active'] ?? null,
            theme: $data['theme'] ?? null,
        );
    }

    public static function fromModel($dashboard): self
    {
        return new self(
            id: $dashboard->id,
            title: $dashboard->title,
            description: $dashboard->description,
            data: $dashboard->data ?? [],
            created_at: $dashboard->created_at?->toISOString(),
            updated_at: $dashboard->updated_at?->toISOString(),
            statistics: $dashboard->statistics ?? null,
            widgets: $dashboard->widgets ?? null,
            charts: $dashboard->charts ?? null,
            user_preferences: $dashboard->user_preferences ?? null,
            layout: $dashboard->layout ?? null,
            is_active: $dashboard->is_active ?? null,
            theme: $dashboard->theme ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'statistics' => $this->statistics,
            'widgets' => $this->widgets,
            'charts' => $this->charts,
            'user_preferences' => $this->user_preferences,
            'layout' => $this->layout,
            'is_active' => $this->is_active,
            'theme' => $this->theme,
        ];
    }

    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    public function hasStatistics(): bool
    {
        return !empty($this->statistics);
    }

    public function hasWidgets(): bool
    {
        return !empty($this->widgets);
    }

    public function hasCharts(): bool
    {
        return !empty($this->charts);
    }

    public function getUserPreference(string $key, $default = null)
    {
        return $this->user_preferences[$key] ?? $default;
    }

    public function getWidgetCount(): int
    {
        return count($this->widgets ?? []);
    }

    public function getChartCount(): int
    {
        return count($this->charts ?? []);
    }

    public function getLayout(): string
    {
        return $this->layout ?? 'default';
    }

    public function getTheme(): string
    {
        return $this->theme ?? 'light';
    }

    /**
     * Dashboard'ın boş olup olmadığını kontrol et
     */
    public function isEmpty(): bool
    {
        return empty($this->widgets) && empty($this->charts) && empty($this->statistics);
    }

    /**
     * Dashboard'ın özel tema kullanıp kullanmadığını kontrol et
     */
    public function hasCustomTheme(): bool
    {
        return !is_null($this->theme) && $this->theme !== 'light';
    }

    /**
     * Dashboard'ın özel layout kullanıp kullanmadığını kontrol et
     */
    public function hasCustomLayout(): bool
    {
        return !is_null($this->layout) && $this->layout !== 'default';
    }

    /**
     * Dashboard'ın kullanıcı tercihleri olup olmadığını kontrol et
     */
    public function hasUserPreferences(): bool
    {
        return !empty($this->user_preferences);
    }

    /**
     * Dashboard'ın veri sayısını döndür
     */
    public function getDataCount(): int
    {
        return count($this->data);
    }

    /**
     * Dashboard'ın istatistik sayısını döndür
     */
    public function getStatisticsCount(): int
    {
        return count($this->statistics ?? []);
    }

    /**
     * Dashboard'ın toplam bileşen sayısını döndür
     */
    public function getTotalComponentCount(): int
    {
        return $this->getWidgetCount() + $this->getChartCount() + $this->getStatisticsCount();
    }

    /**
     * Dashboard'ın kısa açıklamasını döndür
     */
    public function getShortDescription(): string
    {
        return strlen($this->description) > 100 
            ? substr($this->description, 0, 100) . '...' 
            : $this->description;
    }

    /**
     * Dashboard'ın oluşturulma tarihini formatlı döndür
     */
    public function getFormattedCreatedAt(): string
    {
        if (!$this->created_at) return '-';
        return date('d.m.Y H:i', strtotime($this->created_at));
    }

    /**
     * Dashboard'ın güncellenme tarihini formatlı döndür
     */
    public function getFormattedUpdatedAt(): string
    {
        if (!$this->updated_at) return '-';
        return date('d.m.Y H:i', strtotime($this->updated_at));
    }
} 