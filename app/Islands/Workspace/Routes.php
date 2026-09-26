<?php

use App\Islands\Workspace\WorkspaceIslandController;
use Illuminate\Support\Facades\Route;

/*
| Loaded by the islands package into a group of its own. Prefix, name prefix and
| the base middleware come from `config/laravel-islands.php`; everything here is
| relative to `admin/islands/workspace` and named `islands.workspace.*`.
*/

Route::put('settings', [WorkspaceIslandController::class, 'update'])->name('update');
