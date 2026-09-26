<?php

declare(strict_types=1);

namespace App\Islands\AppSettings\Writers;

use App\Models\AppSetting;

class AppSettingsWriter
{
    /**
     * @param  array{registrationEnabled: bool}  $settings
     */
    public function save(array $settings): void
    {
        AppSetting::set('registration_enabled', $settings['registrationEnabled']);
    }
}
