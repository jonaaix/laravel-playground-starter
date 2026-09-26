<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Islands\Users\UsersProps;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Users extends IslandPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    public function getTitle(): string
    {
        return __('Users');
    }

    protected function islandName(): string
    {
        return 'Users';
    }

    protected function islandProps(): array
    {
        return app(UsersProps::class)->build(request());
    }
}
