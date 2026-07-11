<?php

use App\Http\Controllers\Api\EventApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('/events',          [EventApiController::class, 'index'])->name('events.index');
    Route::get('/events/stats',    [EventApiController::class, 'stats'])->name('events.stats');
    Route::get('/events/categories', [EventApiController::class, 'categories'])->name('events.categories');
    Route::get('/events/{id}',     [EventApiController::class, 'show'])->name('events.show');
    Route::get('/search', [EventApiController::class, 'index'])->name('search');
});