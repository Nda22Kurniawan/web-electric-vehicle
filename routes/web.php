<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerFeedbackController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceScheduleController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WorkOrderPartController;
use App\Http\Controllers\WorkOrderServiceController;

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
    return view('landing');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Dashboard Routes
Route::middleware('auth')->group(function () {
    // Admin routes
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        // User management routes (admin only)
        Route::resource('users', UserController::class);
        
        // Admin resource routes
        Route::resource('service-categories', ServiceCategoryController::class);
        Route::resource('parts', PartController::class);
        Route::resource('inventory-transactions', InventoryTransactionController::class);
    });
    
    // Mechanic routes
    Route::middleware('can:mechanic')->group(function () {
        Route::get('/mechanic/dashboard', function () {
            return view('mechanic.dashboard');
        })->name('mechanic.dashboard');
        
        // Mechanic specific routes
        Route::resource('work-orders', WorkOrderController::class);
        Route::resource('work-order-parts', WorkOrderPartController::class);
        Route::resource('work-order-services', WorkOrderServiceController::class);
    });
    
    // Common routes for all authenticated users
    // Appointments with custom routes
    Route::resource('appointments', AppointmentController::class);
    Route::get('/appointments/today', [AppointmentController::class, 'today'])->name('appointments.today');
    Route::get('/appointments/track', [AppointmentController::class, 'track'])->name('appointments.track');
    
    Route::resource('services', ServiceController::class);
    Route::resource('service-schedules', ServiceScheduleController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('customer-feedback', CustomerFeedbackController::class);
    
    // Profile route
    Route::get('/profile', function() {
        return view('profile.index');
    })->name('profile.index');
    
    // Settings route
    Route::get('/settings', function() {
        return view('settings.index');
    })->name('settings.index');
    
    // Notifications route
    Route::get('/notifications', function() {
        return view('notifications.index');
    })->name('notifications.index');
    
    // Redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('mechanic.dashboard');
        }
    })->name('dashboard');
});