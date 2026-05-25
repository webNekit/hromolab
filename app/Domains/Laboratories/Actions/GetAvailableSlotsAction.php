<?php

declare(strict_types=1);

namespace App\Domains\Laboratories\Actions;

use App\Domains\Laboratories\DataTransferObjects\SlotDTO;
use App\Domains\Laboratories\Models\AppointmentSlot;
use App\Domains\Laboratories\Models\LaboratoryWorkingHour;
use Illuminate\Support\Carbon;

class GetAvailableSlotsAction
{
    /**
     * Get available time slots for a laboratory on a given date.
     *
     * @return array<int, SlotDTO>
     */
    public function execute(int $laboratoryId, Carbon $date): array
    {
        $dayOfWeek = (int) $date->format('w'); // 0=Sunday, 1=Monday...

        // Get working hours for this day
        $workingHours = LaboratoryWorkingHour::where('laboratory_id', $laboratoryId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($workingHours === null) {
            return [];
        }

        // Generate all possible slots
        $allSlots = $workingHours->generateSlots($date->format('Y-m-d'));

        // Get booked slots for this date
        $bookedSlots = AppointmentSlot::where('laboratory_id', $laboratoryId)
            ->whereDate('slot_datetime', $date->format('Y-m-d'))
            ->get()
            ->keyBy(fn ($slot): string => $slot->slot_datetime->format('H:i'));

        // Only show future slots if date is today
        $now = now();
        $isToday = $date->isToday();

        $result = [];

        foreach ($allSlots as $time) {
            // Skip past slots if today
            if ($isToday) {
                $slotTime = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d').' '.$time);
                if ($slotTime->lt($now)) {
                    continue;
                }
            }

            $booked = $bookedSlots->get($time);

            $result[] = new SlotDTO(
                time: $time,
                available: $booked === null || $booked->hasCapacity(),
                booked: $booked?->booked ?? 0,
                capacity: $booked?->capacity ?? 1,
            );
        }

        return $result;
    }
}
