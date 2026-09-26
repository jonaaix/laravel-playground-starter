<?php

declare(strict_types=1);

namespace App\Islands\Workspace\Writers;

use App\Models\AppSetting;

class WorkspaceSettingsWriter
{
    /**
     * @param  array{registrationEnabled: bool}  $settings
     */
    public function save(array $settings): void
    {
        AppSetting::set('registration_enabled', $settings['registrationEnabled']);
    }
}
