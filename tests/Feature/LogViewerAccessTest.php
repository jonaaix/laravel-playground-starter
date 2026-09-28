<?php

use App\Models\User;

it('opens the log viewer for a signed-in user', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('log-viewer.index'))
        ->assertOk();
});

it('returns 403 for guests', function () {
    $this->get(route('log-viewer.index'))->assertForbidden();
});

it('returns 403 for a disabled user', function () {
    $this->actingAs(User::factory()->disabled()->create())
        ->get(route('log-viewer.index'))
        ->assertForbidden();
});
