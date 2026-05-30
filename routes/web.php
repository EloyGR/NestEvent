<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('home');
})->name('home');


// Route::post('/sign-in', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/sign-in', [AuthController::class, 'showSignInRoleSelection'])->name('sign-in');
Route::get('/sign-in/form/{role}', [AuthController::class, 'showSignInForm'])->name('sign-in.form');
Route::post('/sign-in', [AuthController::class, 'signIn'])->name('sign-in.submit');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/events', [EventController::class, 'index'])->middleware('auth')->name('events.index');
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth')->name('events.create');
Route::get('/events/my-events', [EventController::class, 'myEvents'])->middleware('auth')->name('events.myEvents');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
Route::post('/events', [EventController::class, 'store'])->middleware('auth')->name('events.store');
Route::get('/events/{id}/edit', [EventController::class, 'edit'])->middleware('auth')->name('events.edit');
Route::put('/events/{id}', [EventController::class, 'update'])->middleware('auth')->name('events.update');
Route::delete('/events/{id}', [EventController::class, 'destroy'])->middleware('auth')->name('events.destroy');

Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->middleware('auth')->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->middleware('auth')->name('users.update');
Route::patch('/users/{id}/password', [UserController::class, 'updatePassword'])->middleware('auth')->name('users.password.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('auth')->name('users.destroy');


Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/create', [VenueController::class, 'create'])->middleware('auth')->name('venues.create');
Route::get('/venues/my-venues', [VenueController::class, 'myVenues'])->middleware('auth')->name('venues.myVenues');
Route::get('/venues/{id}', [VenueController::class, 'show'])->name('venues.show');
Route::post('/venues', [VenueController::class, 'store'])->middleware('auth')->name('venues.store');
Route::post('/venues/{id}/exceptions', [VenueController::class, 'storeException'])->middleware('auth')->name('venues.exceptions.store');
Route::get('/venues/{id}/edit', [VenueController::class, 'edit'])->middleware('auth')->name('venues.edit');
Route::put('/venues/{id}', [VenueController::class, 'update'])->middleware('auth')->name('venues.update');
Route::delete('/venues/{id}', [VenueController::class, 'destroy'])->middleware('auth')->name('venues.destroy');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');


// Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

Route::get('/bookings', [BookingController::class, 'index'])->middleware('auth')->name('bookings.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->middleware('auth')->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->middleware('auth')->name('bookings.store');

Route::get('/notifications', [NotificationController::class, 'index'])->middleware('auth')->name('notifications.index');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->middleware('auth')->name('notifications.show');


// Ruta de depuracion restringida solo a entorno local y usuarios administradores.
if (app()->environment('local')) {
    Route::get('/debug-session', [AuthController::class, 'debugSession'])
        ->middleware(['auth', 'admin'])
        ->name('debug.session');
}

Route::post('/users/{id}/upload-profile-picture', [UserController::class, 'uploadProfilePicture'])->middleware('auth')->name('users.uploadProfilePicture');

Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->middleware(['auth', 'admin'])->name('bookings.updateStatus');

Route::get('/adminpanel', [BookingController::class, 'adminPanel'])
    ->middleware(['auth', 'admin'])
    ->name('adminpanel');

Route::get('/aviso-legal-y-terminos', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/politica-de-privacidad', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/cookies', function () {
    return view('legal.cookies');
})->name('legal.cookies');
