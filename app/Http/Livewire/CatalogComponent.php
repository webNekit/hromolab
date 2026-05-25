<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Catalog\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogComponent extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?int $selectedCategory = null;

    #[Url(as: 'sort')]
    public string $sortBy = 'name_asc';

    #[Url(as: 'popular')]
    public bool $popularOnly = false;

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public ?int $maxLeadTime = null;

    #[Url(as: 'analysis')]
    public ?int $selectedAnalysisId = null;

    protected $listeners = ['cart-updated' => '$refresh'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function applyPriceFilter(?string $min, ?string $max): void
    {
        $this->minPrice = $min !== '' && $min !== null ? (float) $min : null;
        $this->maxPrice = $max !== '' && $max !== null ? (float) $max : null;
        $this->resetPage();
    }

    public function applyLeadTimeFilter(?int $days): void
    {
        $this->maxLeadTime = $days;
        $this->resetPage();
    }

    public function togglePopular(): void
    {
        $this->popularOnly = ! $this->popularOnly;
        $this->resetPage();
    }

    public function addToCart(int $analysisId): void
    {
        $this->dispatch('cart-add', analysisId: $analysisId);
    }

    public function selectAnalysis(int $analysisId): void
    {
        $this->selectedAnalysisId = $analysisId;
    }

    public function closeModal(): void
    {
        $this->selectedAnalysisId = null;
    }

    public function addSelectedToCart(): void
    {
        if ($this->selectedAnalysisId !== null) {
            $this->dispatch('cart-add', analysisId: $this->selectedAnalysisId);
            $this->selectedAnalysisId = null;
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->sortBy = 'name_asc';
        $this->popularOnly = false;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->maxLeadTime = null;
        $this->resetPage();
    }

    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::with('children')
            ->root()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getSelectedAnalysis(): ?Analysis
    {
        if ($this->selectedAnalysisId === null) {
            return null;
        }

        return Analysis::with(['category'])->find($this->selectedAnalysisId);
    }

    public function getPopularAnalyses(): Collection
    {
        return Analysis::popular()
            ->active()
            ->take(10)
            ->get()
            ->pluck('id');
    }

    public function getAnalyses(): LengthAwarePaginator
    {
        $query = Analysis::query()
            ->with('category')
            ->active();

        // Search filter
        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        // Category filter
        if ($this->selectedCategory !== null) {
            $category = Category::find($this->selectedCategory);

            if ($category !== null) {
                $categoryIds = $category->selfAndDescendantIds;
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Popular filter
        if ($this->popularOnly) {
            $query->popular();
        }

        // Price range filter
        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Lead time filter
        if ($this->maxLeadTime !== null) {
            $query->where('lead_time_days', '<=', $this->maxLeadTime);
        }

        // Sorting
        match ($this->sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'popular' => $query->orderBy('is_popular', 'desc')->orderBy('name'),
            default => $query->orderBy('name', 'asc'), // name_asc
        };

        return $query->paginate(12);
    }

    public function render(): View
    {
        return view('livewire.catalog-component', [
            'categories' => $this->getCategories(),
            'analyses' => $this->getAnalyses(),
            'selectedAnalysis' => $this->getSelectedAnalysis(),
            'popularAnalyses' => $this->getPopularAnalyses(),
        ])->layout('layouts.app');
    }
}
