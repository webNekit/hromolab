<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\DataTransferObjects\AnalysisFilterDTO;
use App\Domains\Catalog\Models\Analysis;
use App\Domains\Catalog\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchAnalysesAction
{
    public function execute(AnalysisFilterDTO $dto): LengthAwarePaginator
    {
        $query = Analysis::query()
            ->with('category')
            ->active();

        if ($dto->search !== null && $dto->search !== '') {
            $search = $dto->search;
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($dto->categoryId !== null) {
            $category = Category::find($dto->categoryId);
            if ($category !== null) {
                $query->whereIn('category_id', $category->selfAndDescendantIds);
            }
        }

        if ($dto->popularOnly === true) {
            $query->popular();
        }

        if ($dto->minPrice !== null) {
            $query->where('price', '>=', $dto->minPrice);
        }

        if ($dto->maxPrice !== null) {
            $query->where('price', '<=', $dto->maxPrice);
        }

        if ($dto->maxLeadTime !== null) {
            $query->where('lead_time_days', '<=', $dto->maxLeadTime);
        }

        match ($dto->sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'popular' => $query->orderBy('is_popular', 'desc')->orderBy('name'),
            default => $query->orderBy('name', 'asc'),
        };

        return $query->paginate($dto->perPage);
    }
}
