<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Domains\Auth\Actions\LoginAction;
use App\Domains\Auth\Actions\RegisterAction;
use App\Domains\Auth\DataTransferObjects\LoginDTO;
use App\Domains\Auth\DataTransferObjects\RegisterDTO;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AuthPage extends Component
{
    public string $tab = 'login';

    public string $loginEmail = '';

    public string $loginPassword = '';

    public bool $loginRemember = false;

    public string $registerName = '';

    public string $registerEmail = '';

    public string $registerPhone = '';

    public string $registerPassword = '';

    public string $registerPasswordConfirmation = '';

    public ?string $error = null;

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
        $this->error = null;
    }

    public function login(): void
    {
        $this->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required|min:6',
        ]);

        try {
            app(LoginAction::class)->execute(
                new LoginDTO(
                    email: $this->loginEmail,
                    password: $this->loginPassword,
                    remember: $this->loginRemember,
                )
            );

            $this->redirectIntended(route('patient.dashboard'));
        } catch (\InvalidArgumentException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function register(): void
    {
        $this->validate([
            'registerName' => 'required|min:2',
            'registerEmail' => 'required|email|unique:users,email',
            'registerPhone' => 'nullable|string|max:20',
            'registerPassword' => 'required|min:6',
            'registerPasswordConfirmation' => 'required|same:registerPassword',
        ]);

        try {
            $parts = explode(' ', $this->registerName, 2);

            app(RegisterAction::class)->execute(
                new RegisterDTO(
                    email: $this->registerEmail,
                    password: $this->registerPassword,
                    name: $this->registerName,
                    phone: $this->registerPhone ?: null,
                    firstName: $parts[0] ?? null,
                    lastName: $parts[1] ?? null,
                )
            );

            $this->redirect(route('patient.dashboard'));
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.auth-page')
            ->layout('layouts.app', ['title' => 'Вход / Регистрация — Хромолаб']);
    }
}
