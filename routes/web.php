<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantAuthController;
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

// Main central administration domain routes
Route::get('/', [MainController::class, 'index'])->name('main.index');
Route::post('/clients', [MainController::class, 'store'])->name('main.clients.store');

// Tenant subdomain isolated routes
Route::middleware('tenant')->group(function () {
    // Guest Tenant Routes
    Route::middleware('tenant.guest')->group(function () {
        Route::get('/login', [TenantAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [TenantAuthController::class, 'login'])->name('tenant.login.post');
    });

    // Authenticated Tenant Routes
    Route::middleware('tenant.auth')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'dashboard'])->name('tenant.dashboard');
        Route::post('/users', [TenantController::class, 'createUser'])->name('tenant.users.store');
        Route::post('/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');
    });
});

