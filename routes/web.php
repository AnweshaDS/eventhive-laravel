<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/qr/verify', [QrController::class, 'verify'])->name('qr.verify');
Route::post('/qr/mark-scanned', [QrController::class, 'markScanned'])->name('qr.markScanned');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Attendee routes
Route::middleware(['auth', 'role:attendee,organizer,admin'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});

// Organizer routes
Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('dashboard');
    Route::get('/events', [OrganizerController::class, 'events'])->name('events');
    Route::get('/events/create', [OrganizerController::class, 'create'])->name('create');
    Route::post('/events', [OrganizerController::class, 'store'])->name('store');
    Route::get('/events/{id}/edit', [OrganizerController::class, 'edit'])->name('edit');
    Route::put('/events/{id}', [OrganizerController::class, 'update'])->name('update');
    Route::delete('/events/{id}', [OrganizerController::class, 'destroy'])->name('destroy');
    Route::get('/attendees', [OrganizerController::class, 'attendees'])->name('attendees');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/role', [AdminController::class, 'changeRole'])->name('users.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/events', [AdminController::class, 'events'])->name('events');
    Route::post('/events/status', [AdminController::class, 'changeStatus'])->name('events.status');
    Route::delete('/events/{id}', [AdminController::class, 'deleteEvent'])->name('events.delete');
});