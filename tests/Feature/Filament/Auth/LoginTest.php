<?php

use App\Filament\Auth\Login;
use App\Models\AppSetting;
use App\Models\User;
use Livewire\Livewire;

it('signs in an active user and records the login time', function () {
    $user = User::factory()->create(['last_login_at' => null]);

    Livewire::test(Login::class)
        ->fillForm(['email' => $user->email, 'password' => 'password'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticatedAs($user);
    expect($user->refresh()->last_login_at)->not->toBeNull();
});

it('rejects a disabled user', function () {
    $user = User::factory()->disabled()->create();

    Livewire::test(Login::class)
        ->fillForm(['email' => $user->email, 'password' => 'password'])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

it('offers the sign-up link only while registration is open', function (bool $isOpen) {
    AppSetting::set('registration_enabled', $isOpen);

    $page = Livewire::test(Login::class);

    $isOpen
        ? $page->assertSee(route('filament.admin.auth.register'))
        : $page->assertDontSee(route('filament.admin.auth.register'));
})->with(['open' => true, 'closed' => false]);
