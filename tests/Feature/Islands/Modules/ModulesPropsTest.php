<?php

use App\Filament\Pages\AppSettings;
use App\Filament\Pages\Users;
use App\Islands\Modules\ModulesProps;
use App\Models\User;
use Illuminate\Http\Request;

it('lists every module page under its group, in the group order', function () {
    $this->actingAs(User::factory()->create());

    $props = app(ModulesProps::class)->build(Request::create('/admin/modules'));

    expect($props['groups'])->toHaveCount(2)
        ->and($props['groups'][0])->toMatchArray(['key' => 'administration', 'label' => 'Administration'])
        ->and($props['groups'][0]['entries'])->toHaveCount(1)
        ->and($props['groups'][0]['entries'][0])->toMatchArray(['label' => 'Users', 'url' => Users::getUrl()])
        ->and($props['groups'][0]['entries'][0]['icon'])->toContain('<svg')
        ->and($props['groups'][1])->toMatchArray(['key' => 'system', 'label' => 'System'])
        ->and($props['groups'][1]['entries'][0])->toMatchArray(['label' => 'App Settings', 'url' => AppSettings::getUrl()]);
});

it('opens with the search term from the URL', function () {
    $this->actingAs(User::factory()->create());

    $props = app(ModulesProps::class)->build(Request::create('/admin/modules', parameters: ['q' => ' users ']));

    expect($props['initial'])->toBe(['q' => 'users']);
});
