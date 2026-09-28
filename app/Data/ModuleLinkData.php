<?php

declare(strict_types=1);

namespace App\Data;

use App\Filament\Navigation\ModuleGroupEnum;
use Filament\Support\Icons\Heroicon;
use Spatie\LaravelData\Data;

class ModuleLinkData extends Data
{
    public function __construct(
        public string $label,
        public string $routeName,
        public Heroicon $icon,
        public ModuleGroupEnum $group,
        public string $ability,
    ) {}
}
