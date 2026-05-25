<?php

declare(strict_types=1);

namespace App\Domains\Laboratories\Models;

use Database\Factories\AppointmentSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    /** @use HasFactory<AppointmentSlotFactory> */
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'slot_datetime',
        'capacity',
        'booked',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'slot_datetime' => 'datetime',
            'capacity' => 'integer',
            'booked' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * Scope to only available slots.
     */
    public function scopeAvailable($query): void
    {
        $query->where('is_available', true)
            ->whereColumn('booked', '<', 'capacity');
    }

    /**
     * Check if slot has remaining capacity.
     */
    public function hasCapacity(): bool
    {
        return $this->is_available && $this->booked < $this->capacity;
    }

    /**
     * Book a slot.
     */
    public function book(): bool
    {
        if (! $this->hasCapacity()) {
            return false;
        }

        $this->increment('booked');

        if ($this->booked >= $this->capacity) {
            $this->update(['is_available' => false]);
        }

        return true;
    }
}
