<?php

namespace App\Http\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'pages.home';

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
