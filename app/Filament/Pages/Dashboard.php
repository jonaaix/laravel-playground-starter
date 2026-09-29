<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Islands\Dashboard\DashboardProps;
use BackedEnum;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class Dashboard extends IslandPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = -2;

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function getTitle(): string
    {
        return __('Dashboard');
    }

    protected function islandName(): string
    {
        return 'Dashboard';
    }

    protected function islandProps(): array
    {
        return app(DashboardProps::class)->build(request());
    }
}
