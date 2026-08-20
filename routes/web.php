<?php

use App\Http\Controllers\Auth\SecurityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

    // Security Dashboard
    Route::get('/security', [SecurityController::class, 'index'])->name('security.page');

    // Logout All Other Devices
    Route::post('/logout-all-devices', [SecurityController::class, 'logoutAllDevices'])->name('logout.all');

    // Single Session Logout
    Route::delete('/security/session/{sessionId}', [SecurityController::class, 'logoutSession'])->name('security.logout-session');

    // Trust / Untrust Device
    Route::post('/security/trust-device', [SecurityController::class, 'trustDevice'])->name('security.trust-device');
    Route::delete('/security/untrust-device', [SecurityController::class, 'untrustDevice'])->name('security.untrust-device');

    // Clear Login History
    Route::delete('/security/clear-history', [SecurityController::class, 'clearHistory'])->name('security.clear-history');

    // Real-time Session Count (AJAX)
    Route::get('/security/session-count', [SecurityController::class, 'sessionCount'])->name('security.session-count');
});

require __DIR__ . '/auth.php';
