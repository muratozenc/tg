<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/availabilities', [AvailabilityController::class, 'index'])->name('availabilities');
    Route::post('/availabilitiesAjax', [AvailabilityController::class, 'ajax'])->name('availability_ajax');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::delete('/appointmentsAjax', [AppointmentController::class, 'ajax'])->name('appointment_ajax');

    Route::post('/update-user-timezone', [\App\Http\Controllers\TimeZoneController::class, 'updateUserTimeZone'])->name('update-user-timezone');
});

require __DIR__.'/auth.php';
