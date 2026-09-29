<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\ModuleLinkData;
use App\Filament\Navigation\ModuleLinks;
use App\Services\ModuleVisitRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OpenModuleLinkController extends Controller
{
    public function __construct(
        private readonly ModuleLinks $links,
        private readonly ModuleVisitRecorder $recorder,
    ) {}

    public function __invoke(Request $request, string $moduleRef): RedirectResponse
    {
        $link = collect($this->links->all())->first(fn (ModuleLinkData $link): bool => $link->routeName === $moduleRef);

        abort_if($link === null, 404);

        Gate::authorize($link->ability);

        $this->recorder->record($request->user(), $link->routeName);

        return redirect()->to(route($link->routeName));
    }
}
