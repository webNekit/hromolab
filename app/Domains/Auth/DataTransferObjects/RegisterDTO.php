<?php

declare(strict_types=1);

namespace App\Domains\Auth\DataTransferObjects;

final readonly class RegisterDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $name = null,
        public ?string $phone = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
    ) {}
}
