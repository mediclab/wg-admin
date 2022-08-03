<?php

namespace App\Http\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Main';

    protected static string $view = 'pages.statistics';

    protected static function getNavigationLabel(): string
    {
        return __('Statistics');
    }

    public static function getRoutes(): \Closure
    {
        return static function () {
            Route::get('/statistics', static::class)->name(static::getSlug());
        };
    }

    protected function getWidgets(): array
    {
        return [];
    }

    protected function getTitle(): string
    {
        return __('Server statistics');
    }

    protected function getActions(): array
    {
        return [];
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
