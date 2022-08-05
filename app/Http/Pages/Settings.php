<?php

namespace App\Http\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Route;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'pages.settings';
    protected ?string $maxContentWidth = '7xl';

    public ?string $current_password = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public ?string $language = null;

    protected function getFormSchema(): array
    {
        return [
            Components\Card::make([
                Components\TextInput::make('current_password')
                    ->password()
                    ->rules('required_with:password|min:8'),
                Components\TextInput::make('password')
                    ->password()
                    ->rules('required_with:current_password|min:8|confirmed'),
                Components\TextInput::make('password_confirmation')
                    ->password(),
            ])->columnSpan(1),

            Components\Card::make([
                Components\Select::make('language')
                    ->options([
                        'en' => __('English'),
                    ])
                ->disabled(),
            ])->columnSpan(1),
        ];
    }

    protected function makeForm(): ComponentContainer
    {
        return parent::makeForm()->columns();
    }

    public function mount(): void
    {
        $this->form->fill([
            'language' => 'en',
        ]);
    }

    public function submit(): void
    {
        $this->validate();

        $user = auth()->user();

        if ($user && ! \Hash::check($this->current_password, $user->password)) {
            Notification::make()
                ->title(__('Passwords does not match'))
                ->danger()
                ->persistent()
                ->send()
            ;

            $this->reset('current_password', 'password', 'password_confirmation');

            return;
        }

        try {
            $user->update(['password' => \Hash::make($this->password)]);

            Notification::make()
                ->title(__('Password successfully changed'))
                ->success()
                ->send()
            ;
        } catch (\Throwable $e) {
            \Log::error('Error user password change', ['exception' => $e]);

            Notification::make()
                ->title(__('Error occurred. Password is not changed'))
                ->danger()
                ->persistent()
                ->send()
            ;
        }

        $this->reset('current_password', 'password', 'password_confirmation');
    }

    public static function getSlug(): string
    {
        return 'settings';
    }

    public static function getRoutes(): \Closure
    {
        return static function () {
            Route::get('/settings', static::class)->name(static::getSlug());
        };
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
