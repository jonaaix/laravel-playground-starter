<?php

use App\Filament\Auth\Register;
use App\Models\AppSetting;
use App\Models\User;
use Livewire\Livewire;

it('renders the registration page while registration is open', function () {
    AppSetting::set('registration_enabled', true);

    $this->get(route('filament.admin.auth.register'))->assertOk();
});

it('returns 404 for the registration page while registration is closed', function () {
    AppSetting::set('registration_enabled', false);

    $this->get(route('filament.admin.auth.register'))->assertNotFound();
});

it('refuses a submission once registration was closed after the form opened', function () {
    AppSetting::set('registration_enabled', true);

    $page = Livewire::test(Register::class)->fillForm([
        'name' => 'Late Person',
        'email' => 'late@example.com',
        'password' => 'password123',
        'passwordConfirmation' => 'password123',
    ]);

    AppSetting::set('registration_enabled', false);

    $page->call('register')->assertStatus(404);

    expect(User::query()->where('email', 'late@example.com')->exists())->toBeFalse();
});
