<?php

declare(strict_types=1);

namespace App\Islands\Workspace\Queries;

use App\Models\AppSetting;

class WorkspaceSettingsQuery
{
    /**
     * @return array{registrationEnabled: bool}
     */
    public function settings(): array
    {
        return [
            'registrationEnabled' => (bool) AppSetting::get('registration_enabled', true),
        ];
    }
}
