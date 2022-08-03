<?php

namespace App\Providers;

use App\Http\Pages\Dashboard;
use App\Http\Pages\Home;
use App\Http\Pages\Settings;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Yepsua\Filament\Themes\Facades\FilamentThemes;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Filament::registerScripts([
            asset('assets/js/extend.js')
        ], true);

        Filament::registerRenderHook(
            'body.start',
            static fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
        );

        Filament::serving(static function () {
            Filament::registerUserMenuItems([
                'settings' => UserMenuItem::make()
                    ->icon('heroicon-o-cog')
                    ->label(__('Settings'))
                    ->url(route(Settings::getRouteName())),
            ]);
        });
    }
}
