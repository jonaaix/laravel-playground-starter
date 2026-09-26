<?php

declare(strict_types=1);

namespace App\Islands\Users;

use Aaix\LaravelIslands\IslandRoutes;
use App\Islands\Users\Queries\UsersQuery;
use Illuminate\Http\Request;

class UsersProps
{
    /**
     * @return array<string, mixed>
     */
    public function build(Request $request): array
    {
        return [
            'dataUrl' => IslandRoutes::route('users', 'data'),
            'storeUrl' => IslandRoutes::route('users', 'store'),
            'updateUrl' => IslandRoutes::route('users', 'update', ['user' => '__ID__']),
            'destroyUrl' => IslandRoutes::route('users', 'destroy', ['user' => '__ID__']),
            'perPageOptions' => UsersQuery::PER_PAGE_OPTIONS,
            'initial' => UsersQuery::state($request),
        ];
    }
}
