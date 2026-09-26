<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use App\Models\AppSetting;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getSubheading(): string|Htmlable|null
    {
        if (blank($this->userUndertakingMultiFactorAuthentication) && ! AppSetting::get('registration_enabled', true)) {
            return null;
        }

        return parent::getSubheading();
    }
}
