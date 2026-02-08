<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/intelligence', [App\Http\Controllers\Admin\IntelligenceController::class, 'index'])->name('intelligence.index');
    Route::get('/intelligence/{signal}', [App\Http\Controllers\Admin\IntelligenceController::class, 'show'])->name('intelligence.show');
    Route::post('/intelligence/{signal}/override', [App\Http\Controllers\Admin\IntelligenceController::class, 'override'])->name('intelligence.override');

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/usage', [App\Http\Controllers\Admin\UsageController::class, 'index'])->name('usage.index');
    Route::post('/upgrade/request', [App\Http\Controllers\Admin\SettingsController::class, 'requestUpgrade'])->name('upgrade.request');
});

// Onboarding Flow
Route::middleware(['auth', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/student', [App\Http\Controllers\Admin\OnboardingController::class, 'student'])->name('student');
    Route::post('/student', [App\Http\Controllers\Admin\OnboardingController::class, 'storeStudent'])->name('student.store');
});
