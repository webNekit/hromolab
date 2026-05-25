<?php

declare(strict_types=1);

namespace App\Domains\Orders\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Laboratories\Models\Laboratory;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'order_number',
        'appointment_datetime',
        'total_price',
        'payment_status',
        'current_status',
        'payment_id',
        'promo_code',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'appointment_datetime' => 'datetime',
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status): void
    {
        $query->where('current_status', $status);
    }

    /**
     * Scope to filter by payment status.
     */
    public function scopeWithPaymentStatus($query, string $status): void
    {
        $query->where('payment_status', $status);
    }

    /**
     * Scope to orders for a specific user.
     */
    public function scopeForUser($query, User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        $query->where('user_id', $userId);
    }

    /**
     * Update order status and record history.
     */
    public function updateStatus(string $newStatus, ?User $changedBy = null, ?string $comment = null): void
    {
        $previousStatus = $this->current_status;

        $this->update([
            'current_status' => $newStatus,
        ]);

        $this->statusHistories()->create([
            'changed_by_user_id' => $changedBy?->id,
            'status' => $newStatus,
            'previous_status' => $previousStatus,
            'comment' => $comment,
        ]);
    }

    /**
     * Check if all order items have been completed.
     */
    public function areAllItemsCompleted(): bool
    {
        return $this->items()->whereHas('medicalResult', function ($q): void {
            $q->whereNotNull('verified_at');
        })->count() === $this->items->count();
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;

        return "HL-{$date}-".str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
