<?php

declare(strict_types=1);

namespace App\Islands\Users\State;

use Aaix\LaravelIslandsDatagrid\ViewProfiles\ViewProfileSchema;
use Aaix\LaravelIslandsDatagrid\ViewProfiles\ViewProfileStore;
use App\Enums\DatagridSectionEnum;
use App\Islands\Users\Queries\UsersQuery;

class UsersViewProfiles
{
    public static function store(): ViewProfileStore
    {
        return new ViewProfileStore(DatagridSectionEnum::Users, self::schema());
    }

    private static function schema(): ViewProfileSchema
    {
        return ViewProfileSchema::make()
            ->text('q')
            ->choice('status', UsersQuery::STATUSES)
            ->choice('sort', UsersQuery::SORTABLE)
            ->choice('dir', ['asc', 'desc'])
            ->choice('perPage', UsersQuery::PER_PAGE_OPTIONS)
            ->keyList('cols', UsersPreferences::COLUMNS);
    }
}
