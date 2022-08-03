<?php

declare(strict_types=1);

namespace App\Http\Components\Auth;

use Illuminate\Contracts\View\View;

class Login extends \Filament\Http\Livewire\Auth\Login
{
    public function render(): View
    {
        return view('filament::login')
            ->layout('layouts.card', [
                'title' => __('Login to Dashboard'),
            ]);
    }
}
