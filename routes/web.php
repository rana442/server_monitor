<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SystemStatsController;
use App\Http\Middleware\AdminMiddleware;


// Simple Auth Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
   
    
    // Password Reset Routes
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// User Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/partial', [DashboardController::class, 'partial'])
    ->name('dashboard.partial');

    
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/theme', [ProfileController::class, 'toggleTheme'])->name('profile.theme');
    
    // Monitors (User)
    Route::get('/monitors/{monitor}', [MonitorController::class, 'show'])->name('monitors.show');
    Route::get('/monitors/{monitor}/logs', [MonitorController::class, 'logs'])->name('monitors.logs');


});

// Admin Routes - সরাসরি Middleware ক্লাস ব্যবহার করুন (Temporary Fix)
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Monitors (Admin CRUD)
    Route::resource('/monitors', MonitorController::class)->except(['show']);
    Route::get('/monitors/{monitor}', [MonitorController::class, 'show'])
    ->name('monitors.show');

    Route::post('/monitors/{monitor}/test', [MonitorController::class, 'test'])->name('monitors.test');
    Route::post('/monitors/{monitor}/reset', [MonitorController::class, 'reset'])->name('monitors.reset');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::put('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    
    // System Logs
    Route::get('/system-logs', [AdminController::class, 'systemLogs'])->name('system.logs');

    // System stats route (for web)
    Route::get('/system-stats', [SystemStatsController::class, 'getStats'])
        ->middleware('auth')
        ->name('system.stats');

     // Register
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->name('register_process');
});

// Test Routes (ডিবাগিং এর জন্য - ব্যবহার শেষে মুছে ফেলুন)
Route::get('/test-admin', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'is_admin' => auth()->check() ? auth()->user()->is_admin : false,
        'user' => auth()->check() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'is_admin' => auth()->user()->is_admin,
        ] : null,
    ]);
})->middleware(['auth', AdminMiddleware::class]);

Route::get('/test-auth', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'is_admin' => auth()->check() ? auth()->user()->is_admin : false,
    ]);
})->middleware('auth');