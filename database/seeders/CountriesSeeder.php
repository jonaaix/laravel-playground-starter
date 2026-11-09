<?php

declare(strict_types=1);

namespace Database\Seeders;

use Aaix\LaravelCountries\Database\Seeders\DatabaseSeeder as CountriesPackageSeeder;
use Aaix\LaravelCountries\Models\Country;
use App\Models\AppSetting;
use Composer\InstalledVersions;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public const string SEEDED_VERSION_SETTING = 'countries.seeded_version';

    private const string PACKAGE = 'aaix/laravel-countries';

    public function run(): void
    {
        $installedVersion = self::installedVersion();

        if ($this->isUpToDate($installedVersion)) {
            return;
        }

        $this->call(CountriesPackageSeeder::class);

        AppSetting::set(self::SEEDED_VERSION_SETTING, $installedVersion);
    }

    public static function installedVersion(): string
    {
        return InstalledVersions::getReference(self::PACKAGE) ?? InstalledVersions::getPrettyVersion(self::PACKAGE) ?? 'unknown';
    }

    private function isUpToDate(string $installedVersion): bool
    {
        return AppSetting::get(self::SEEDED_VERSION_SETTING) === $installedVersion
            && Country::query()->exists();
    }
}
