<?php

declare(strict_types=1);

namespace App\Domains\Catalog\DataTransferObjects;

final readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?int $parentId = null,
        public ?string $description = null,
        public int $sortOrder = 0,
        public bool $isActive = true,
    ) {}
}
