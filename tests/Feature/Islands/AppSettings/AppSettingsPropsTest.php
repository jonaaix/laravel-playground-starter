<?php

use App\Islands\AppSettings\AppSettingsProps;
use Illuminate\Http\Request;

it('opens the tab named in the URL', function () {
    $props = app(AppSettingsProps::class)->build(Request::create('/admin/app-settings', parameters: ['tab' => 'general']));

    expect($props['initial']['tab'])->toBe('general');
});

it('falls back to the first tab for an unknown one', function () {
    $props = app(AppSettingsProps::class)->build(Request::create('/admin/app-settings', parameters: ['tab' => 'nope']));

    expect($props['initial']['tab'])->toBe('general');
});
