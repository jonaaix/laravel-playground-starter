<?php

use App\Models\AppSetting;
use App\Models\User;

it('returns 401 for guests', function () {
    $this->putJson(route('islands.workspace.update'), ['registrationEnabled' => false])->assertUnauthorized();
});

it('returns 403 for a disabled user', function () {
    $this->actingAs(User::factory()->disabled()->create())
        ->putJson(route('islands.workspace.update'), ['registrationEnabled' => false])
        ->assertForbidden();
});

it('turns public registration off', function () {
    AppSetting::set('registration_enabled', true);

    $this->actingAs(User::factory()->create())
        ->putJson(route('islands.workspace.update'), ['registrationEnabled' => false])
        ->assertOk()
        ->assertJsonPath('data.registrationEnabled', false);

    expect(AppSetting::get('registration_enabled'))->toBeFalse();
});

it('rejects a value that is not a boolean', function () {
    $this->actingAs(User::factory()->create())
        ->putJson(route('islands.workspace.update'), ['registrationEnabled' => 'maybe'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['registrationEnabled' => 'The registration enabled field must be true or false.']);
});
