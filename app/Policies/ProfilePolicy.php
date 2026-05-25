<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Auth\Models\Profile;
use App\Domains\Auth\Models\User;

class ProfilePolicy
{
    public function view(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id || $user->hasRole('super-admin');
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id;
    }
}
