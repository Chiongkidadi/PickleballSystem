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

// Redirect the root URL directly to the kiosk page
Route::redirect('/', '/kiosk');

// Public Kiosk Routes
Route::controller(BookingController::class)->group(function () {
    // This loads the 'kiosk' view via the kioskReserve method in your controller
    Route::get('/kiosk', 'kioskReserve')->name('kiosk.reserve');
    Route::post('/reserve', 'store')->name('reserve.store');
    
    // Email Verification (OTP) for public bookings
    Route::post('/send-otp', 'sendOtp')->name('otp.send');
    Route::post('/verify-otp', 'verifyOtp')->name('otp.verify');
});

// Basic Login Redirect
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin Authentication Routes (Guest only)
Route::prefix('admin-panel')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showEmailForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'sendOtp'])->name('login.send');
    Route::get('/verify', [AdminAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify', [AdminAuthController::class, 'verifyOtp'])->name('otp.verify');
});

// Admin Dashboard Routes (Authenticated only)
Route::prefix('admin-panel')->name('admin.')->middleware('auth')->group(function () {
    // Main Dashboard with filters
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Booking Management
    Route::get('/reserve', [BookingController::class, 'adminReserve'])->name('reserve');
    Route::get('/walk-in', [AdminDashboardController::class, 'walkin'])->name('walkin');
    Route::post('/approve/{id}', [AdminDashboardController::class, 'approve'])->name('reservations.approve');
    
    // Utilities
    Route::get('/print-summary', [BookingController::class, 'printSummary'])->name('bookings.print');
    Route::post('/update-promo', [AdminDashboardController::class, 'updatePromo'])->name('updatePromo');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});