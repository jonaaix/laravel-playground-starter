<?php

use App\Models\ModuleVisit;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('counts the visit and forwards to the tool', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('module-links.open', ['moduleRef' => 'log-viewer.index']))
        ->assertRedirect(route('log-viewer.index'));

    expect(ModuleVisit::query()->whereBelongsTo($user)->sole())
        ->module_ref->toBe('log-viewer.index')
        ->visit_count->toBe(1);
});

it('redirects guests to the sign-in page', function () {
    $this->get(route('module-links.open', ['moduleRef' => 'log-viewer.index']))
        ->assertRedirect(route('filament.admin.auth.login'));
});

it('returns 404 for a link that is not registered', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('module-links.open', ['moduleRef' => 'filament.admin.pages.users']))
        ->assertNotFound();
});

it('returns 403 and counts nothing when the user may not open the tool', function () {
    Gate::define('viewLogViewer', fn (): bool => false);

    $this->actingAs(User::factory()->create())
        ->get(route('module-links.open', ['moduleRef' => 'log-viewer.index']))
        ->assertForbidden();

    expect(ModuleVisit::query()->count())->toBe(0);
});
