<?php

declare(strict_types=1);

namespace App\Domains\Catalog\DataTransferObjects;

final readonly class AnalysisFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?bool $popularOnly = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?int $maxLeadTime = null,
        public string $sortBy = 'name_asc',
        public int $perPage = 12,
    ) {}
}
