<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Catalog\Models\Analysis;

class AnalysisPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Analysis $analysis): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function update(User $user, Analysis $analysis): bool
    {
        return $user->hasRole('super-admin');
    }

    public function delete(User $user, Analysis $analysis): bool
    {
        return $user->hasRole('super-admin');
    }

    public function restore(User $user, Analysis $analysis): bool
    {
        return $user->hasRole('super-admin');
    }

    public function forceDelete(User $user, Analysis $analysis): bool
    {
        return $user->hasRole('super-admin');
    }

    public function import(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function export(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
}
