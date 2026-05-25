<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Laboratories\Models\Laboratory;

class LaboratoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Laboratory $laboratory): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function update(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('super-admin');
    }

    public function delete(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('super-admin');
    }
}
