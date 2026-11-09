<?php

declare(strict_types=1);

use Aaix\LaravelCountries\Models\Country;
use App\Models\AppSetting;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\DatabaseSeeder;

it('seeds the countries and records the package version on an empty database', function (): void {
    $this->seed(DatabaseSeeder::class);

    expect(Country::query()->where('iso_alpha_2', 'DE')->exists())->toBeTrue()
        ->and(AppSetting::get(CountriesSeeder::SEEDED_VERSION_SETTING))->toBe(CountriesSeeder::installedVersion());
});

it('does not seed again while the recorded package version is installed', function (): void {
    $this->seed(CountriesSeeder::class);
    Country::query()->where('iso_alpha_2', 'DE')->delete();

    $this->seed(CountriesSeeder::class);

    expect(Country::query()->where('iso_alpha_2', 'DE')->exists())->toBeFalse();
});

it('seeds again after the package version changed', function (): void {
    $this->seed(CountriesSeeder::class);
    Country::query()->where('iso_alpha_2', 'DE')->delete();
    AppSetting::set(CountriesSeeder::SEEDED_VERSION_SETTING, 'previous-version');

    $this->seed(CountriesSeeder::class);

    expect(Country::query()->where('iso_alpha_2', 'DE')->exists())->toBeTrue()
        ->and(AppSetting::get(CountriesSeeder::SEEDED_VERSION_SETTING))->toBe(CountriesSeeder::installedVersion());
});
