<?php

declare(strict_types=1);

namespace App\Filament\Search;

use Aaix\LaravelIslandsSearch\Contracts\SearchSource;
use Aaix\LaravelIslandsSearch\SearchHit;
use App\Islands\Modules\Queries\ModulesCatalogQuery;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;

class ModulesSearchSource implements SearchSource
{
    public function __construct(private readonly ModulesCatalogQuery $catalog) {}

    public function key(): string
    {
        return 'modules';
    }

    public function label(): string
    {
        return __('Modules');
    }

    public function isVisibleTo(Authenticatable $user): bool
    {
        return true;
    }

    public function search(string $query, int $limit): array
    {
        $needle = mb_strtolower(trim($query));

        return collect($this->catalog->entries(Filament::getCurrentOrDefaultPanel()))
            ->filter(fn (array $entry): bool => str_contains(mb_strtolower($entry['label']), $needle)
                || str_contains(mb_strtolower($entry['group']->label()), $needle))
            ->take($limit)
            ->map(fn (array $entry): SearchHit => new SearchHit(
                title: $entry['label'],
                url: $entry['url'],
                subtitle: $entry['group']->label(),
                icon: $entry['icon']?->value,
            ))
            ->values()
            ->all();
    }
}
