<?php

namespace App\Http\Pages;

use App\Http\Widgets\DevicesTable;
use App\Http\Widgets\StatsOverview;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-desktop-computer';

    protected static ?int $navigationSort = -2;

    protected static string $view = 'filament::pages.dashboard';

    protected static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('filament::pages/dashboard.title');
    }

    public static function getSlug(): string
    {
        return 'dashboard';
    }

    public static function getRoutes(): \Closure
    {
        return static function () {
            Route::get('/dashboard', static::class)->name(static::getSlug());
        };
    }

    protected function getWidgets(): array
    {
        return [
            StatsOverview::class,
            DevicesTable::class,
        ];
    }

    protected function getTitle(): string
    {
        return static::$title ?? __('filament::pages/dashboard.title');
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return (bool) \Auth::user()?->isAdmin();
    }

    public function mount(): void
    {
        abort_unless((bool) \Auth::user()?->isAdmin(), 403);
    }

    protected function getColumns(): int | array
    {
        return 2;
    }
}
