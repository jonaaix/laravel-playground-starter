<?php

declare(strict_types=1);

namespace App\Islands\AppSettings;

use Aaix\LaravelIslands\IslandRoutes;
use App\Islands\AppSettings\Queries\AppSettingsQuery;
use Illuminate\Http\Request;

class AppSettingsProps
{
    public const array TABS = ['general'];

    public function __construct(private readonly AppSettingsQuery $query) {}

    /**
     * @return array{updateUrl: string, settings: array{registrationEnabled: bool}, tabs: list<string>, initial: array{tab: string}}
     */
    public function build(Request $request): array
    {
        $tab = $request->string('tab')->toString();

        return [
            'updateUrl' => IslandRoutes::route('app-settings', 'update'),
            'settings' => $this->query->settings(),
            'tabs' => self::TABS,
            'initial' => [
                'tab' => in_array($tab, self::TABS, true) ? $tab : self::TABS[0],
            ],
        ];
    }
}
