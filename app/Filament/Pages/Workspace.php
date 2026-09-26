<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Islands\Workspace\WorkspaceProps;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Workspace extends IslandPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Workspace');
    }

    public function getTitle(): string
    {
        return __('Workspace');
    }

    protected function islandName(): string
    {
        return 'Workspace';
    }

    protected function islandProps(): array
    {
        return app(WorkspaceProps::class)->build(request());
    }
}
