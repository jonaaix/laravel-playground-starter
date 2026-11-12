<?php

declare(strict_types=1);

namespace App\Islands\Users;

use Aaix\LaravelIslands\IslandRoutes;
use App\Islands\Users\Queries\UsersQuery;
use App\Islands\Users\State\UsersPreferences;
use App\Islands\Users\State\UsersViewProfiles;
use Illuminate\Http\Request;

class UsersProps
{
    /**
     * @return array<string, mixed>
     */
    public function build(Request $request): array
    {
        $profiles = UsersViewProfiles::store();
        $userId = $request->user()?->id;
        $ref = $request->string('view')->toString();

        return [
            'dataUrl' => IslandRoutes::route('users', 'data'),
            'storeUrl' => IslandRoutes::route('users', 'store'),
            'updateUrl' => IslandRoutes::route('users', 'update', ['user' => '__ID__']),
            'destroyUrl' => IslandRoutes::route('users', 'destroy', ['user' => '__ID__']),
            'preferencesUrl' => IslandRoutes::route('users', 'preferences'),
            'profilesUrl' => IslandRoutes::route('users', 'profiles.store'),
            'profileUrl' => IslandRoutes::route('users', 'profiles.update', ['profile' => '__REF__']),
            'profiles' => $profiles->forUser($userId),
            'activeProfile' => $ref !== '' ? $profiles->shared($ref, $userId) : null,
            'preferences' => UsersPreferences::store()->forUser($userId),
            'perPageOptions' => UsersQuery::PER_PAGE_OPTIONS,
            'initial' => UsersQuery::state($request),
        ];
    }
}
