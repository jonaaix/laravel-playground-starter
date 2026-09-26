<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Islands\Modules\ModulesProps;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Modules extends IslandPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = -1;

    public static function getNavigationLabel(): string
    {
        return __('Modules');
    }

    public function getTitle(): string
    {
        return __('Modules');
    }

    protected function islandName(): string
    {
        return 'Modules';
    }

    protected function islandProps(): array
    {
        return app(ModulesProps::class)->build(request());
    }
}
