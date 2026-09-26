<?php

declare(strict_types=1);

namespace App\Islands\Workspace;

use App\Islands\Workspace\Queries\WorkspaceSettingsQuery;
use App\Islands\Workspace\Writers\WorkspaceSettingsWriter;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WorkspaceIslandController extends Controller
{
    public function __construct(
        private readonly WorkspaceSettingsQuery $query,
        private readonly WorkspaceSettingsWriter $writer,
    ) {}

    public function update(Request $request): JsonResponse
    {
        $this->authorizeAccess($request);

        $settings = $request->validate([
            'registrationEnabled' => ['required', 'boolean'],
        ]);

        $this->writer->save($settings);

        return response()->json(['data' => $this->query->settings()]);
    }

    private function authorizeAccess(Request $request): void
    {
        if (! ($request->user()?->canAccessPanel(Filament::getPanel('admin')) ?? false)) {
            throw new AccessDeniedHttpException;
        }
    }
}
