<?php

use App\Islands\Users\UsersIslandController;
use Illuminate\Support\Facades\Route;

/*
| Loaded by the islands package into a group of its own. Prefix, name prefix and
| the base middleware come from `config/laravel-islands.php`; everything here is
| relative to `admin/islands/users` and named `islands.users.*`.
*/

Route::get('data', [UsersIslandController::class, 'data'])->name('data');
Route::post('users', [UsersIslandController::class, 'store'])->name('store');
Route::put('users/{user}', [UsersIslandController::class, 'update'])->name('update');
Route::delete('users/{user}', [UsersIslandController::class, 'destroy'])->name('destroy');
Route::patch('preferences', [UsersIslandController::class, 'updatePreference'])->name('preferences');
Route::post('profiles', [UsersIslandController::class, 'storeViewProfile'])->name('profiles.store');
Route::patch('profiles/{profile}', [UsersIslandController::class, 'updateViewProfile'])->name('profiles.update');
Route::delete('profiles/{profile}', [UsersIslandController::class, 'destroyViewProfile'])->name('profiles.destroy');
