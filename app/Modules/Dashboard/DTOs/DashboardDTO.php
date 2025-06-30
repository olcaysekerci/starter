<?php

namespace App\Modules\Dashboard\DTOs;

class DashboardDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly array $data
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'],
            data: $data['data'] ?? []
        );
    }
} 