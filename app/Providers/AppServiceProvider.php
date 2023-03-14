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
