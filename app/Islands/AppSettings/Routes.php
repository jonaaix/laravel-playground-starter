<?php

use App\Islands\AppSettings\AppSettingsIslandController;
use Illuminate\Support\Facades\Route;

/*
| Loaded by the islands package into a group of its own. Prefix, name prefix and
| the base middleware come from `config/laravel-islands.php`; everything here is
| relative to `admin/islands/app-settings` and named `islands.app-settings.*`.
*/

Route::put('settings', [AppSettingsIslandController::class, 'update'])->name('update');
