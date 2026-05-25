<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Orders\Models\OrderItem;
use Database\Factories\AnalysisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Analysis extends Model
{
    /** @use HasFactory<AnalysisFactory> */
    use HasFactory;

    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'preparation',
        'biomaterial',
        'price',
        'lead_time_days',
        'reference_ranges',
        'is_active',
        'is_popular',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'lead_time_days' => 'integer',
            'reference_ranges' => 'array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to only active analyses.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to only popular analyses.
     */
    public function scopePopular($query): void
    {
        $query->where('is_popular', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, int $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopePriceRange($query, ?float $min = null, ?float $max = null): void
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
    }

    /**
     * Scope to filter by lead time (days).
     */
    public function scopeLeadTimeUpTo($query, int $days): void
    {
        $query->where('lead_time_days', '<=', $days);
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'biomaterial' => $this->biomaterial,
        ];
    }

    /**
     * Determine which Scout driver to use.
     */
    public function searchableAs(): string
    {
        return config('scout.prefix').'analyses';
    }
}
