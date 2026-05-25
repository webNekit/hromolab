<?php

declare(strict_types=1);

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Catalog\Models\Category;
use App\Http\Livewire\CatalogComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Not seeding by default - tests create their own data
});

it('renders the catalog page successfully', function (): void {
    get('/catalog')
        ->assertSuccessful()
        ->assertSee('Каталог анализов');
});

it('displays analyses in the catalog', function (): void {
    Analysis::factory(5)->active()->create();

    Livewire::test(CatalogComponent::class)
        ->assertSuccessful()
        ->assertSet('sortBy', 'name_asc')
        ->assertViewHas('analyses');
});

it('filters by category', function (): void {
    $category = Category::factory()->create();
    $analysisInCategory = Analysis::factory()->active()->create(['category_id' => $category->id]);
    $analysisOther = Analysis::factory()->active()->create();

    Livewire::test(CatalogComponent::class)
        ->set('selectedCategory', $category->id)
        ->assertViewHas('analyses', function ($analyses) use ($analysisInCategory) {
            return $analyses->contains('id', $analysisInCategory->id);
        });
});

it('filters by search query', function (): void {
    $analysis = Analysis::factory()->active()->create(['name' => 'УникальныйТестовыйАнализ']);
    Analysis::factory(5)->active()->create();

    Livewire::test(CatalogComponent::class)
        ->set('search', 'УникальныйТестовыйАнализ')
        ->assertViewHas('analyses', function ($analyses) use ($analysis) {
            return $analyses->contains('id', $analysis->id);
        });
});

it('filters by popular only', function (): void {
    Analysis::factory(3)->active()->popular()->create();
    Analysis::factory(5)->active()->create(['is_popular' => false]);

    Livewire::test(CatalogComponent::class)
        ->call('togglePopular')
        ->assertSet('popularOnly', true)
        ->assertViewHas('analyses', function ($analyses) {
            return $analyses->count() === 3;
        });
});

it('sorts by price ascending', function (): void {
    Analysis::factory()->active()->create(['price' => 5000, 'name' => 'A']);
    Analysis::factory()->active()->create(['price' => 1000, 'name' => 'B']);
    Analysis::factory()->active()->create(['price' => 300, 'name' => 'C']);

    Livewire::test(CatalogComponent::class)
        ->set('sortBy', 'price_asc')
        ->assertViewHas('analyses', function ($analyses) {
            $prices = $analyses->pluck('price')->toArray();
            $sorted = $prices;
            sort($sorted);

            return $prices === $sorted;
        });
});

it('sorts by price descending', function (): void {
    Analysis::factory()->active()->create(['price' => 300, 'name' => 'A']);
    Analysis::factory()->active()->create(['price' => 5000, 'name' => 'B']);
    Analysis::factory()->active()->create(['price' => 1000, 'name' => 'C']);

    Livewire::test(CatalogComponent::class)
        ->set('sortBy', 'price_desc')
        ->assertViewHas('analyses', function ($analyses) {
            $prices = $analyses->pluck('price')->toArray();
            $sorted = $prices;
            rsort($sorted);

            return $prices === $sorted;
        });
});

it('filters by price range', function (): void {
    Analysis::factory()->active()->create(['price' => 500, 'name' => 'A']);
    Analysis::factory()->active()->create(['price' => 1500, 'name' => 'B']);
    Analysis::factory()->active()->create(['price' => 3000, 'name' => 'C']);

    Livewire::test(CatalogComponent::class)
        ->call('applyPriceFilter', '1000', '2000')
        ->assertViewHas('analyses', function ($analyses) {
            return $analyses->count() === 1 && (float) $analyses->first()->price === 1500.0;
        });
});

it('filters by lead time', function (): void {
    Analysis::factory()->active()->create(['lead_time_days' => 1, 'name' => 'A']);
    Analysis::factory()->active()->create(['lead_time_days' => 5, 'name' => 'B']);
    Analysis::factory()->active()->create(['lead_time_days' => 14, 'name' => 'C']);

    Livewire::test(CatalogComponent::class)
        ->call('applyLeadTimeFilter', 3)
        ->assertViewHas('analyses', function ($analyses) {
            return $analyses->count() === 1 && $analyses->first()->lead_time_days === 1;
        });
});

it('shows analysis detail modal', function (): void {
    $analysis = Analysis::factory()->active()->create();

    Livewire::test(CatalogComponent::class)
        ->call('selectAnalysis', $analysis->id)
        ->assertSet('selectedAnalysisId', $analysis->id)
        ->assertViewHas('selectedAnalysis');
});

it('closes analysis detail modal', function (): void {
    $analysis = Analysis::factory()->active()->create();

    Livewire::test(CatalogComponent::class)
        ->call('selectAnalysis', $analysis->id)
        ->call('closeModal')
        ->assertSet('selectedAnalysisId', null);
});

it('resets page on filter changes', function (): void {
    Livewire::test(CatalogComponent::class)
        ->set('search', 'test')
        ->assertSet('search', 'test');
});

it('clears all filters', function (): void {
    $category = Category::factory()->create();

    Livewire::test(CatalogComponent::class)
        ->set('search', 'test')
        ->set('selectedCategory', $category->id)
        ->set('sortBy', 'price_desc')
        ->set('popularOnly', true)
        ->set('minPrice', 1000)
        ->set('maxPrice', 5000)
        ->set('maxLeadTime', 7)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('selectedCategory', null)
        ->assertSet('sortBy', 'name_asc')
        ->assertSet('popularOnly', false)
        ->assertSet('minPrice', null)
        ->assertSet('maxPrice', null)
        ->assertSet('maxLeadTime', null);
});

it('paginates results', function (): void {
    Analysis::factory(20)->active()->create();

    Livewire::test(CatalogComponent::class)
        ->assertViewHas('analyses', function ($analyses) {
            return $analyses->count() === 12; // 12 per page
        });
});

it('dispatches cart-add event', function (): void {
    $analysis = Analysis::factory()->active()->create();

    Livewire::test(CatalogComponent::class)
        ->call('addToCart', $analysis->id)
        ->assertDispatched('cart-add');
});

it('shows categories in sidebar', function (): void {
    Category::factory(3)->create();

    Livewire::test(CatalogComponent::class)
        ->assertViewHas('categories');
});
