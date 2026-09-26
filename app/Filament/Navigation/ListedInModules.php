<?php

declare(strict_types=1);

namespace App\Filament\Navigation;

interface ListedInModules
{
    public static function getModuleGroup(): ModuleGroupEnum;
}
