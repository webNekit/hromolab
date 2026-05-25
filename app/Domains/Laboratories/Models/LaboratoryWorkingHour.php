<?php

declare(strict_types=1);

namespace App\Domains\Laboratories\Models;

use Carbon\Carbon;
use Database\Factories\LaboratoryWorkingHourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratoryWorkingHour extends Model
{
    /** @use HasFactory<LaboratoryWorkingHourFactory> */
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'day_of_week',
        'open_time',
        'close_time',
        'slot_interval_minutes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'slot_interval_minutes' => 'integer',
            'open_time' => 'datetime:H:i',
            'close_time' => 'datetime:H:i',
        ];
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * Generate time slots for this working hour configuration.
     *
     * @return array<int, string>
     */
    public function generateSlots(string $date): array
    {
        $slots = [];
        $open = Carbon::parse($date.' '.$this->open_time->format('H:i'));
        $close = Carbon::parse($date.' '.$this->close_time->format('H:i'));
        $interval = $this->slot_interval_minutes;

        $current = $open->copy();
        while ($current->lt($close)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($interval);
        }

        return $slots;
    }
}
