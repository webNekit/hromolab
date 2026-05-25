<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class)->active();
    }

    /**
     * Scope to only root categories (no parent).
     */
    public function scopeRoot($query): void
    {
        $query->whereNull('parent_id');
    }

    /**
     * Scope to only active categories.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get all descendant categories recursively.
     *
     * @return Collection<int, Category>
     */
    public function getDescendantsAttribute(): Collection
    {
        $descendants = collect();
        $this->loadMissing('children');

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants);
        }

        return $descendants;
    }

    /**
     * Get all category IDs including self and descendants.
     *
     * @return array<int, int>
     */
    public function getSelfAndDescendantIdsAttribute(): array
    {
        return $this->descendants
            ->pluck('id')
            ->push($this->id)
            ->unique()
            ->values()
            ->toArray();
    }
}
