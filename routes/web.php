<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Redirect root to the public kiosk
Route::redirect('/', '/kiosk');

// 2. Public Kiosk Routes & Booking Submission
Route::controller(BookingController::class)->group(function () {
    Route::get('/kiosk', 'kioskReserve')->name('kiosk.reserve');
    Route::post('/reserve', 'store')->name('reserve.store');
});

// --- ADDED THIS FIX ---
// Laravel's security automatically looks for a route named exactly 'login'
// This safely catches it and points it to your custom admin login page!
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
// ----------------------

// 3. Admin Authentication Routes (Only for users who are NOT logged in)
Route::prefix('admin-panel')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showEmailForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'sendOtp'])->name('login.send');
    Route::get('/verify', [AdminAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify', [AdminAuthController::class, 'verifyOtp'])->name('otp.verify');
});

// 4. Admin Panel Routes (Protected! Requires login)
Route::prefix('admin-panel')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reserve', [BookingController::class, 'adminReserve'])->name('reserve');
    Route::get('/walk-in', [AdminDashboardController::class, 'walkin'])->name('walkin');
    Route::post('/approve/{id}', [AdminDashboardController::class, 'approve'])->name('reservations.approve');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});