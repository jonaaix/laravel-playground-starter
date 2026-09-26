<?php

declare(strict_types=1);

namespace App\Islands\AppSettings\Queries;

use App\Models\AppSetting;

class AppSettingsQuery
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
