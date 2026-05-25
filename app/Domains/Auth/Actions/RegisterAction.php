<?php

declare(strict_types=1);

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\DataTransferObjects\RegisterDTO;
use App\Domains\Auth\Models\Profile;
use App\Domains\Auth\Models\User;
use Spatie\Permission\Models\Role;

class RegisterAction
{
    public function execute(RegisterDTO $dto): User
    {
        $user = User::create([
            'name' => $dto->name ?? ($dto->firstName.' '.$dto->lastName),
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => bcrypt($dto->password),
        ]);

        $role = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $user->assignRole($role);

        if ($dto->firstName || $dto->lastName) {
            Profile::create([
                'user_id' => $user->id,
                'first_name' => $dto->firstName ?? '',
                'last_name' => $dto->lastName ?? '',
                'middle_name' => $dto->middleName,
                'birth_date' => $dto->birthDate,
                'gender' => $dto->gender,
            ]);
        }

        auth()->login($user);

        return $user;
    }
}
