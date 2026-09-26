<?php

use App\Filament\Pages\Users;
use App\Filament\Pages\Workspace;
use App\Models\User;

it('redirects guests to the sign-in page', function (string $page) {
    $this->get($page::getUrl())->assertRedirect(route('filament.admin.auth.login'));
})->with([Users::class, Workspace::class]);

it('mounts the island for a signed-in user', function (string $page, string $island) {
    $this->actingAs(User::factory()->create())
        ->get($page::getUrl())
        ->assertOk()
        ->assertSee('data-island', escape: false)
        ->assertSee($island);
})->with([
    [Users::class, 'Users'],
    [Workspace::class, 'Workspace'],
]);

it('returns 403 for a disabled user', function () {
    $this->actingAs(User::factory()->disabled()->create())
        ->get(Users::getUrl())
        ->assertForbidden();
});
