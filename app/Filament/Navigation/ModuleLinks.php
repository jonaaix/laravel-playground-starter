<?php

declare(strict_types=1);

namespace App\Filament\Navigation;

use App\Data\ModuleLinkData;
use Filament\Support\Icons\Heroicon;

class ModuleLinks
{
    /**
     * @return list<ModuleLinkData>
     */
    public function all(): array
    {
        return [
            new ModuleLinkData(
                label: __('Log Viewer'),
                routeName: 'log-viewer.index',
                icon: Heroicon::OutlinedDocumentText,
                group: ModuleGroupEnum::System,
                ability: 'viewLogViewer',
            ),
        ];
    }
}
