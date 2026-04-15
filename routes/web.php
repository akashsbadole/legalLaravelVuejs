<?php

use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

// Apply audit middleware to authenticated routes
Route::middleware(['auth', 'audit'])->group(function () {
    // All authenticated web routes will be audited
});

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/home', function () {
    return view('landing');
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
})->name('health');

// Contact Routes
Route::post('/contact/submit', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// SPA catch-all - MUST be last
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::post('tenants/{tenant}/extend-subscription', [\App\Http\Controllers\TenantController::class, 'extendSubscription'])->name('tenants.extend-subscription');
    Route::patch('tenants/{tenant}/suspend', [\App\Http\Controllers\TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::patch('tenants/{tenant}/activate', [\App\Http\Controllers\TenantController::class, 'activate'])->name('tenants.activate');
});
