<?php

namespace App\Http\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;

class Home extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -3;

    protected static string $view = 'pages.home';

    public static function getSlug(): string
    {
        return 'home';
    }

    public static function getRoutes(): \Closure
    {
        return static function () {
            Route::get('/home', static::class)->name(static::getSlug());
        };
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return \Auth::user()?->isAdmin();
    }

    /**
     * @return string|null
     */
    public function getHeading(): string | Htmlable
    {
        return '';
    }
}
