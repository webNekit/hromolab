<?php

declare(strict_types=1);

namespace App\Domains\Laboratories\DataTransferObjects;

final readonly class SlotDTO
{
    public function __construct(
        public string $time,
        public bool $available,
        public int $booked,
        public int $capacity,
    ) {}

    /**
     * @return array{time: string, available: bool, booked: int, capacity: int}
     */
    public function toArray(): array
    {
        return [
            'time' => $this->time,
            'available' => $this->available,
            'booked' => $this->booked,
            'capacity' => $this->capacity,
        ];
    }
}
