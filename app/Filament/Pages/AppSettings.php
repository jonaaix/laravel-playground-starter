<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Navigation\ListedInModules;
use App\Filament\Navigation\ModuleGroupEnum;
use App\Islands\AppSettings\AppSettingsProps;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class AppSettings extends IslandPage implements ListedInModules
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'app-settings';

    public static function getModuleGroup(): ModuleGroupEnum
    {
        return ModuleGroupEnum::System;
    }

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }

    public function getTitle(): string
    {
        return __('App Settings');
    }

    protected function islandName(): string
    {
        return 'AppSettings';
    }

    protected function islandProps(): array
    {
        return app(AppSettingsProps::class)->build(request());
    }
}
