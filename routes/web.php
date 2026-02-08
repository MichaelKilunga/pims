<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/intelligence', [App\Http\Controllers\Admin\IntelligenceController::class, 'index'])->name('intelligence.index');
    Route::get('/intelligence/{signal}', [App\Http\Controllers\Admin\IntelligenceController::class, 'show'])->name('intelligence.show');
    Route::post('/intelligence/{signal}/override', [App\Http\Controllers\Admin\IntelligenceController::class, 'override'])->name('intelligence.override');

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/usage', [App\Http\Controllers\Admin\UsageController::class, 'index'])->name('usage.index');
});

// Auth Routes (Minimal for now)
Route::get('/login', function() {
    // For demo: automatically log in the first user
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        return redirect()->route('admin.dashboard');
    }
    return "No user found to auto-login. Please seed the database.";
})->name('login');
