<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactsPage extends Component
{
    public string $contactName = '';

    public string $contactEmail = '';

    public string $contactMessage = '';

    public ?string $contactSent = null;

    public function submitContact(): void
    {
        $this->validate([
            'contactName' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'contactMessage' => 'required|string|min:10|max:5000',
        ]);

        $this->contactSent = 'Спасибо! Ваше сообщение отправлено. Мы ответим вам в ближайшее время.';
        $this->reset('contactName', 'contactEmail', 'contactMessage');
    }

    public function render(): View
    {
        return view('livewire.contacts-page')
            ->layout('layouts.app', ['title' => 'Контакты — Хромолаб']);
    }
}
