<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Orders\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'lab-assistant']);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole(['super-admin', 'lab-assistant']);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('super-admin');
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->hasRole('super-admin');
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasRole('super-admin');
    }
}
