<?php

declare(strict_types=1);

namespace App\Filament\Navigation;

use Filament\Support\Icons\Heroicon;

enum ModuleGroupEnum: string
{
    case Administration = 'administration';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Administration => __('Administration'),
            self::System => __('System'),
        };
    }

    public function icon(): Heroicon
    {
        return match ($this) {
            self::Administration => Heroicon::OutlinedShieldCheck,
            self::System => Heroicon::OutlinedServerStack,
        };
    }

    public function accent(): string
    {
        return match ($this) {
            self::Administration => '180',
            self::System => 'muted',
        };
    }
}
