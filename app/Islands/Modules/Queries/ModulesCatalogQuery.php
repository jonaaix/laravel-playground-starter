<?php

declare(strict_types=1);

namespace App\Islands\Modules\Queries;

use App\Filament\Navigation\ListedInModules;
use App\Filament\Navigation\ModuleGroupEnum;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;

class ModulesCatalogQuery
{
    /**
     * @return list<array{key: string, label: string, icon: string, accent: string, entries: list<array{label: string, url: string, icon: string}>}>
     */
    public function groups(Panel $panel): array
    {
        /** @var list<class-string<Page&ListedInModules>> $pages */
        $pages = collect($panel->getPages())
            ->filter(fn (string $page): bool => is_subclass_of($page, ListedInModules::class) && $page::canAccess())
            ->values()
            ->all();

        return collect(ModuleGroupEnum::cases())
            ->map(fn (ModuleGroupEnum $group): array => [
                'key' => $group->value,
                'label' => $group->label(),
                'icon' => $this->iconSvg($group->icon()),
                'accent' => $group->accent(),
                'entries' => collect($pages)
                    ->filter(fn (string $page): bool => $page::getModuleGroup() === $group)
                    ->map(fn (string $page): array => [
                        'label' => $page::getNavigationLabel(),
                        'url' => $page::getUrl(panel: $panel->getId()),
                        'icon' => $this->iconSvg($page::getNavigationIcon()),
                    ])
                    ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => $group['entries'] !== [])
            ->values()
            ->all();
    }

    private function iconSvg(mixed $icon): string
    {
        if (! $icon instanceof BackedEnum) {
            return '';
        }

        return svg('heroicon-'.$icon->value)->toHtml();
    }
}
