<?php

declare(strict_types=1);

namespace App\Islands\Users\State;

use Aaix\LaravelIslandsDatagrid\Preferences\PreferenceSchema;
use Aaix\LaravelIslandsDatagrid\Preferences\PreferenceStore;
use App\Enums\DatagridSectionEnum;

class UsersPreferences
{
    public const array COLUMNS = ['name', 'email', 'status', 'last_login_at', 'created_at', 'actions'];

    public static function store(): PreferenceStore
    {
        return new PreferenceStore(DatagridSectionEnum::Users, PreferenceSchema::make()->columns(self::COLUMNS));
    }
}
