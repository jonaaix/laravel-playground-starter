<?php

use App\Filament\Pages\AppSettings;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Modules;
use App\Filament\Pages\Users;
use App\Models\ModuleVisit;
use App\Models\User;

it('redirects guests to the sign-in page', function (string $page) {
    $this->get($page::getUrl())->assertRedirect(route('filament.admin.auth.login'));
})->with([Dashboard::class, Modules::class, Users::class, AppSettings::class]);

it('mounts the island for a signed-in user', function (string $page, string $island) {
    $this->actingAs(User::factory()->create())
        ->get($page::getUrl())
        ->assertOk()
        ->assertSee('data-island', escape: false)
        ->assertSee($island);
})->with([
    [Dashboard::class, 'Dashboard'],
    [Modules::class, 'Modules'],
    [Users::class, 'Users'],
    [AppSettings::class, 'AppSettings'],
]);

it('returns 403 for a disabled user', function () {
    $this->actingAs(User::factory()->disabled()->create())
        ->get(Users::getUrl())
        ->assertForbidden();
});

it('counts every visit of a catalog page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(Users::getUrl())->assertOk();
    $this->actingAs($user)->get(Users::getUrl())->assertOk();

    expect(ModuleVisit::query()->whereBelongsTo($user)->sole())
        ->module_ref->toBe(Users::getRouteName())
        ->visit_count->toBe(2);
});

it('does not count pages outside the catalog', function () {
    $this->actingAs(User::factory()->create())->get(Dashboard::getUrl())->assertOk();

    expect(ModuleVisit::query()->count())->toBe(0);
});
