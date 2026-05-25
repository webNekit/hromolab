<?php

declare(strict_types=1);

namespace App\Domains\Orders\Models;

use Database\Factories\PromoCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    /** @use HasFactory<PromoCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'uses_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_uses' => 'integer',
            'uses_count' => 'integer',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if the promo code is currently valid.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->format('Y-m-d');

        if ($this->valid_from !== null && $today < $this->valid_from->format('Y-m-d')) {
            return false;
        }

        if ($this->valid_until !== null && $today > $this->valid_until->format('Y-m-d')) {
            return false;
        }

        if ($this->max_uses > 0 && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Increment the usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('uses_count');
    }

    /**
     * Scope to only active promo codes.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }
}
