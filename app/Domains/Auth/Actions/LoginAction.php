<?php

declare(strict_types=1);

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\DataTransferObjects\LoginDTO;
use App\Domains\Auth\Models\User;
use App\Domains\Orders\Services\CartServiceInterface;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function __construct(
        private readonly CartServiceInterface $cartService,
    ) {}

    public function execute(LoginDTO $dto): User
    {
        $user = User::where('email', $dto->email)->first();

        if ($user === null || ! Hash::check($dto->password, $user->password)) {
            throw new \InvalidArgumentException('Неверный email или пароль');
        }

        auth()->login($user, $dto->remember);

        $this->cartService->mergeGuestCartIntoUserCart();

        return $user;
    }
}
