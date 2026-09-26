<?php

declare(strict_types=1);

namespace App\Islands\AppSettings;

use App\Islands\AppSettings\Queries\AppSettingsQuery;
use App\Islands\AppSettings\Writers\AppSettingsWriter;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppSettingsIslandController extends Controller
{
    public function __construct(
        private readonly AppSettingsQuery $query,
        private readonly AppSettingsWriter $writer,
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
