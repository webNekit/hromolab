<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class HomePage extends Component
{
    public function render(): View
    {
        return view('livewire.home-page')
            ->layout('layouts.app', ['title' => 'Хромолаб — лаборатория здоровья']);
    }
}
