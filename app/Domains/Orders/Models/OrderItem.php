<?php

declare(strict_types=1);

namespace App\Domains\Orders\Models;

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Results\Models\MedicalResult;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'analysis_id',
        'price',
        'discount',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public function medicalResult(): HasOne
    {
        return $this->hasOne(MedicalResult::class, 'order_item_id');
    }

    /**
     * Calculate final price after discount.
     */
    public function getFinalPriceAttribute(): float
    {
        return (float) $this->price - (float) $this->discount;
    }

    /**
     * Check if this item has a verified result.
     */
    public function hasVerifiedResult(): bool
    {
        return $this->medicalResult !== null && $this->medicalResult->verified_at !== null;
    }
}
