<?php

use App\Filament\Pages\Users;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('finds a module page by its name', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('filament.admin.islands-search', ['q' => 'use']))
        ->assertOk()
        ->assertJsonPath('data.groups.0.hits.0.title', 'Users')
        ->assertJsonPath('data.groups.0.hits.0.url', Users::getUrl())
        ->assertJsonPath('data.groups.0.hits.0.subtitle', 'Administration');
});

it('finds every entry of a group by the group name', function () {
    $response = $this->actingAs(User::factory()->create())
        ->getJson(route('filament.admin.islands-search', ['q' => 'system']))
        ->assertOk();

    expect(collect($response->json('data.groups.0.hits'))->pluck('title')->all())->toBe(['App Settings', 'Log Viewer']);
});

it('does not offer a tool the user may not open', function () {
    Gate::define('viewLogViewer', fn (): bool => false);

    $response = $this->actingAs(User::factory()->create())
        ->getJson(route('filament.admin.islands-search', ['q' => 'log']))
        ->assertOk();

    expect($response->json('data.groups'))->toBe([]);
});
