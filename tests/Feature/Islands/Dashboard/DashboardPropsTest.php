<?php

use App\Filament\Pages\Dashboard;
use App\Models\User;

it('greets the signed-in user by name', function () {
    $this->actingAs(User::factory()->create(['name' => 'Ada Lovelace']))
        ->get(Dashboard::getUrl())
        ->assertOk()
        ->assertSee('Ada Lovelace');
});
