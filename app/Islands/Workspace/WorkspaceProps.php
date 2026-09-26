<?php

declare(strict_types=1);

namespace App\Islands\Workspace;

use Aaix\LaravelIslands\IslandRoutes;
use App\Islands\Workspace\Queries\WorkspaceSettingsQuery;
use Illuminate\Http\Request;

class WorkspaceProps
{
    public function __construct(private readonly WorkspaceSettingsQuery $query) {}

    /**
     * @return array{updateUrl: string, settings: array{registrationEnabled: bool}}
     */
    public function build(Request $request): array
    {
        return [
            'updateUrl' => IslandRoutes::route('workspace', 'update'),
            'settings' => $this->query->settings(),
        ];
    }
}
