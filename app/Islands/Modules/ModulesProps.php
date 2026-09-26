<?php

declare(strict_types=1);

namespace App\Islands\Modules;

use App\Islands\Modules\Queries\ModulesCatalogQuery;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class ModulesProps
{
    public function __construct(private readonly ModulesCatalogQuery $query) {}

    /**
     * @return array{groups: list<array<string, mixed>>, initial: array{q: string}}
     */
    public function build(Request $request): array
    {
        return [
            'groups' => $this->query->groups(Filament::getCurrentOrDefaultPanel()),
            'initial' => [
                'q' => trim($request->string('q')->toString()),
            ],
        ];
    }
}
