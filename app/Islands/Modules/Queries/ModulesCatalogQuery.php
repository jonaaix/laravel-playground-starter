<?php

declare(strict_types=1);

namespace App\Islands\Modules\Queries;

use App\Data\ModuleLinkData;
use App\Filament\Navigation\ListedInModules;
use App\Filament\Navigation\ModuleGroupEnum;
use App\Filament\Navigation\ModuleLinks;
use BackedEnum;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

class ModulesCatalogQuery
{
    public function __construct(private readonly ModuleLinks $links) {}

    /**
     * @return list<array{key: string, label: string, icon: string, accent: string, entries: list<array{label: string, url: string, icon: string}>}>
     */
    public function groups(Panel $panel): array
    {
        $entries = $this->entries($panel);

        return collect(ModuleGroupEnum::cases())
            ->map(fn (ModuleGroupEnum $group): array => [
                'key' => $group->value,
                'label' => $group->label(),
                'icon' => $this->iconSvg($group->icon()),
                'accent' => $group->accent(),
                'entries' => collect($entries)
                    ->filter(fn (array $entry): bool => $entry['group'] === $group)
                    ->map(fn (array $entry): array => [
                        'label' => $entry['label'],
                        'url' => $entry['url'],
                        'icon' => $this->iconSvg($entry['icon']),
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => $group['entries'] !== [])
            ->values()
            ->all();
    }

    /**
     * @return list<array{group: ModuleGroupEnum, label: string, url: string, icon: ?BackedEnum}>
     */
    public function entries(Panel $panel): array
    {
        return collect([...$this->pageEntries($panel), ...$this->linkEntries()])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * @return list<array{group: ModuleGroupEnum, label: string, url: string, icon: ?BackedEnum}>
     */
    private function pageEntries(Panel $panel): array
    {
        return collect($panel->getPages())
            ->filter(fn (string $page): bool => is_subclass_of($page, ListedInModules::class) && $page::canAccess())
            ->map(fn (string $page): array => [
                'group' => $page::getModuleGroup(),
                'label' => $page::getNavigationLabel(),
                'url' => $page::getUrl(panel: $panel->getId()),
                'icon' => $page::getNavigationIcon() instanceof BackedEnum ? $page::getNavigationIcon() : null,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{group: ModuleGroupEnum, label: string, url: string, icon: ?BackedEnum}>
     */
    private function linkEntries(): array
    {
        return collect($this->links->all())
            ->filter(fn (ModuleLinkData $link): bool => Gate::allows($link->ability))
            ->map(fn (ModuleLinkData $link): array => [
                'group' => $link->group,
                'label' => $link->label,
                'url' => route($link->routeName),
                'icon' => $link->icon,
            ])
            ->values()
            ->all();
    }

    private function iconSvg(?BackedEnum $icon): string
    {
        return $icon === null ? '' : svg('heroicon-'.$icon->value)->toHtml();
    }
}
