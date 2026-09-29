<?php

use App\Filament\Pages\AppSettings;
use App\Filament\Pages\Users;
use App\Islands\Dashboard\Queries\QuickAccessQuery;
use App\Models\ModuleVisit;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;

function quickAccessFor(User $user): array
{
    return app(QuickAccessQuery::class)->entries($user, Filament::getPanel('admin'));
}

it('ranks a recent module above one that was used often but long ago', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    ModuleVisit::factory()->for($user)->create(['module_ref' => Users::getRouteName(), 'visit_count' => 10, 'last_visited_at' => now()->subDays(60)]);
    ModuleVisit::factory()->for($user)->create(['module_ref' => AppSettings::getRouteName(), 'visit_count' => 2, 'last_visited_at' => now()]);

    expect(array_column(quickAccessFor($user), 'label'))->toBe(['App Settings', 'Users']);
});

it('ranks the more often used module first when both were used at the same time', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    ModuleVisit::factory()->for($user)->create(['module_ref' => Users::getRouteName(), 'visit_count' => 3, 'last_visited_at' => now()]);
    ModuleVisit::factory()->for($user)->create(['module_ref' => AppSettings::getRouteName(), 'visit_count' => 8, 'last_visited_at' => now()]);

    expect(array_column(quickAccessFor($user), 'label'))->toBe(['App Settings', 'Users']);
});

it('leaves out modules that are gone or that the user may not open', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Gate::define('viewLogViewer', fn (): bool => false);
    ModuleVisit::factory()->for($user)->create(['module_ref' => 'log-viewer.index']);
    ModuleVisit::factory()->for($user)->create(['module_ref' => 'filament.admin.pages.removed-module']);

    expect(quickAccessFor($user))->toBe([]);
});

it('only uses the visits of the given user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    ModuleVisit::factory()->create(['module_ref' => Users::getRouteName()]);

    expect(quickAccessFor($user))->toBe([]);
});
