<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use App\Models\AppSetting;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;

class Register extends BaseRegister
{
    public function mount(): void
    {
        $this->ensureRegistrationIsOpen();

        parent::mount();
    }

    public function register(): ?RegistrationResponse
    {
        $this->ensureRegistrationIsOpen();

        return parent::register();
    }

    private function ensureRegistrationIsOpen(): void
    {
        abort_unless(AppSetting::get('registration_enabled', true), 404);
    }
}
