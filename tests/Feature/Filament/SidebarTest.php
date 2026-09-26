<?php

use App\Filament\Pages\AppSettings;
use App\Filament\Pages\Modules;
use App\Filament\Pages\Users;
use App\Models\User;
use Filament\Pages\Dashboard;

it('shows only the dashboard and the module catalog in the sidebar', function () {
    $this->actingAs(User::factory()->create())
        ->get(Dashboard::getUrl())
        ->assertOk()
        ->assertSee(Modules::getUrl())
        ->assertDontSee(Users::getUrl())
        ->assertDontSee(AppSettings::getUrl());
});
