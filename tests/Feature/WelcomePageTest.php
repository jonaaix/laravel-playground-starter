<?php

use App\Models\AppSetting;

it('links to sign-up only while registration is open', function (bool $isOpen) {
    AppSetting::set('registration_enabled', $isOpen);

    $response = $this->get(route('home'))->assertOk();

    $isOpen
        ? $response->assertSee(route('filament.admin.auth.register'))
        : $response->assertDontSee(route('filament.admin.auth.register'));
})->with(['open' => true, 'closed' => false]);
