<?php

declare(strict_types=1);

namespace App\Islands\Dashboard;

use App\Filament\Pages\Modules;
use App\Islands\Dashboard\Queries\QuickAccessQuery;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class DashboardProps
{
    public function __construct(private readonly QuickAccessQuery $quickAccess) {}

    /**
     * @return array{userName: string, quickAccess: list<array{label: string, group: string, url: string, icon: string}>, modulesUrl: string}
     */
    public function build(Request $request): array
    {
        $user = $request->user();

        return [
            'userName' => (string) $user?->name,
            'quickAccess' => $user === null ? [] : $this->quickAccess->entries($user, Filament::getCurrentOrDefaultPanel()),
            'modulesUrl' => Modules::getUrl(),
        ];
    }
}
