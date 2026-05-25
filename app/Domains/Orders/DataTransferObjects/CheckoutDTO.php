<?php

declare(strict_types=1);

namespace App\Domains\Orders\DataTransferObjects;

use DateTimeInterface;

final readonly class CheckoutDTO
{
    /**
     * @param  array<int, array{analysis_id: int, quantity: int}>  $items
     */
    public function __construct(
        public ?int $userId,
        public int $laboratoryId,
        public DateTimeInterface $appointmentDatetime,
        public array $items,
        public string $paymentMethod,
        public ?string $promoCode = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
    ) {}
}
