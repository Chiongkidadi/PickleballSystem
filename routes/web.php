<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AdminAuthController;

Route::redirect('/', '/kiosk');

// Public Kiosk Routes
Route::controller(BookingController::class)->group(function () {
    Route::get('/kiosk', 'kioskReserve')->name('reserve.index');
    Route::post('/kiosk', 'store')->name('reserve.store');
    Route::get('/booking-success', function () { return view('bookings.success'); })->name('booking.success');

    // FIXED: Updated these paths to match your JavaScript calls
    Route::post('/otp/send', 'sendOtp')->name('otp.send');
    Route::post('/otp/verify', 'verifyOtp')->name('otp.verify');

    // The Reschedule Route for your email button
    Route::get('/kiosk/reschedule/{id}', 'rescheduleFromEmail')
        ->name('reserve.reschedule')
        ->middleware('signed');
});

Route::get('/login', function () { return redirect()->route('admin.login'); })->name('login');

// Admin Authentication
Route::prefix('admin-panel')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showEmailForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'sendOtp'])->name('login.send');
    Route::get('/verify', [AdminAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify', [AdminAuthController::class, 'verifyOtp'])->name('otp.verify');
});

// Admin Dashboard
Route::prefix('admin-panel')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin Reservation Routes
    Route::get('/reserve', [BookingController::class, 'adminReserve'])->name('reserve');
    // ADDED: This POST route allows the Admin Walk-in "Confirm" button to save data
    Route::post('/reserve', [BookingController::class, 'store'])->name('reserve.store'); 
    
    Route::get('/walk-in', [AdminDashboardController::class, 'walkin'])->name('walkin');

    Route::patch('/bookings/{id}/approve', [BookingController::class, 'approve'])->name('reservations.approve');
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('reservations.cancel');
    
    Route::get('/print-summary', [BookingController::class, 'printSummary'])->name('bookings.print');
    Route::post('/update-promo', [AdminDashboardController::class, 'updatePromo'])->name('updatePromo');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});