<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Results\Models\MedicalResult;

class MedicalResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'lab-assistant']);
    }

    public function view(User $user, MedicalResult $medicalResult): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('lab-assistant')) {
            return true;
        }

        return $medicalResult->orderItem->order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'lab-assistant']);
    }

    public function update(User $user, MedicalResult $medicalResult): bool
    {
        return $user->hasRole(['super-admin', 'lab-assistant']);
    }

    public function delete(User $user, MedicalResult $medicalResult): bool
    {
        return $user->hasRole('super-admin');
    }

    public function verify(User $user): bool
    {
        return $user->hasRole(['lab-assistant', 'super-admin']);
    }
}
