<?php

use App\Http\Controllers\OpenModuleLinkController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('admin/open/{moduleRef}', OpenModuleLinkController::class)->name('module-links.open');
});
