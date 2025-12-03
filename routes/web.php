<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenueController;

use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/sign-in', function () {
    return view('sign-in');
})->name('sign-in');



// Route::post('/sign-in', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/sign-in', [AuthController::class, 'showSignInForm'])->name('sign-in');
Route::post('/sign-in', [AuthController::class, 'signIn'])->name('sign-in');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/events', [EventController::class, 'index'])->middleware('auth')->name('events.index');
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::get('/events/my-events', [EventController::class, 'myEvents'])->name('events.myEvents');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
Route::post('/events', [EventController::class, 'store'])->name('events.store');

Route::get('/events/my-events', [EventController::class, 'myEvents'])->name('events.myEvents');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');


Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/create', [VenueController::class, 'create'])->name('venues.create');
Route::get('/venues/my-venues', [VenueController::class, 'myVenues'])->name('venues.myVenues');
Route::get('/venues/{id}', [VenueController::class, 'show'])->name('venues.show');
Route::post('/venues', [VenueController::class, 'store'])->name('venues.store');





// Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');


Route::get('/debug-session', [AuthController::class, 'debugSession'])->name('debug.session');

Route::post('/users/{id}/upload-profile-picture', [UserController::class, 'uploadProfilePicture'])->name('users.uploadProfilePicture');
