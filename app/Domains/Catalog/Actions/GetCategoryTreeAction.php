<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class GetCategoryTreeAction
{
    public function execute(): Collection
    {
        return Category::with('children')
            ->root()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
