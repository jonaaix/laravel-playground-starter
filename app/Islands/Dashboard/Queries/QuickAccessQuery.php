<?php

declare(strict_types=1);

namespace App\Islands\Dashboard\Queries;

use App\Islands\Modules\Queries\ModulesCatalogQuery;
use App\Models\ModuleVisit;
use App\Models\User;
use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Panel;

class QuickAccessQuery
{
    public const int LIMIT = 6;

    private const int HALF_LIFE_DAYS = 14;

    public function __construct(private readonly ModulesCatalogQuery $catalog) {}

    /**
     * @return list<array{label: string, group: string, url: string, icon: string}>
     */
    public function entries(User $user, Panel $panel): array
    {
        $catalog = collect($this->catalog->entries($panel))->keyBy('ref');

        return ModuleVisit::query()
            ->whereBelongsTo($user)
            ->whereIn('module_ref', $catalog->keys())
            ->get()
            ->sortByDesc(fn (ModuleVisit $visit): float => $this->score($visit->visit_count, $visit->last_visited_at))
            ->take(self::LIMIT)
            ->map(function (ModuleVisit $visit) use ($catalog): array {
                $entry = $catalog[$visit->module_ref];

                return [
                    'label' => $entry['label'],
                    'group' => $entry['group']->label(),
                    'url' => $entry['url'],
                    'icon' => $this->iconSvg($entry['icon']),
                ];
            })
            ->values()
            ->all();
    }

    /* Frecency: every visit counts, and its weight halves with each HALF_LIFE_DAYS since the last one. */
    private function score(int $visitCount, CarbonInterface $lastVisitedAt): float
    {
        $idleDays = max(0.0, $lastVisitedAt->diffInSeconds(now()) / 86400);

        return $visitCount * 0.5 ** ($idleDays / self::HALF_LIFE_DAYS);
    }

    private function iconSvg(?BackedEnum $icon): string
    {
        return $icon === null ? '' : svg('heroicon-'.$icon->value)->toHtml();
    }
}
