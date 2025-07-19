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
use App\Http\Controllers\CustomerController;

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

// Public feedback routes (tidak perlu auth)
Route::get('/feedback/{trackingCode}', [CustomerFeedbackController::class, 'publicForm'])->name('customer-feedback.public-form');
Route::post('/feedback/{trackingCode}', [CustomerFeedbackController::class, 'storePublic'])->name('customer-feedback.store-public');

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

        // Route khusus untuk parts - harus sebelum resource
        Route::get('/parts/low-stock', [PartController::class, 'lowStock'])->name('parts.low-stock');
        Route::resource('parts', PartController::class);

        Route::resource('inventory-transactions', InventoryTransactionController::class);
        Route::get('/inventory-transactions/report', [InventoryTransactionController::class, 'report'])->name('inventory-transactions.report');
        Route::get('/inventory-transactions/movement', [InventoryTransactionController::class, 'movementReport'])->name('inventory-transactions.movement');

        Route::resource('work-orders', WorkOrderController::class);
        Route::get('/work-orders/invoice', [WorkOrderController::class, 'invoice'])->name('work-orders.invoice');
        Route::get('/work-orders/receipt', [WorkOrderController::class, 'receipt'])->name('work-orders.receipt');
        Route::get('/work-orders/show', [WorkOrderController::class, 'show'])->name('work-orders.show');
    });

    // Mechanic routes
    Route::middleware('can:mechanic')->group(function () {
        Route::get('/mechanic/dashboard', function () {
            return view('mechanic.dashboard');
        })->name('mechanic.dashboard');

        // Mechanic specific routes
        Route::resource('work-order-parts', WorkOrderPartController::class);
        Route::resource('work-order-services', WorkOrderServiceController::class);
        Route::resource('work-orders', WorkOrderController::class);
    });

    // Customer routes
    Route::middleware('can:customer')->group(function () {
        Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
        
        // Customer specific routes
        Route::get('/customer/bookings', [CustomerController::class, 'bookings'])->name('customer.bookings');
        Route::get('/customer/bookings/create', [CustomerController::class, 'createBooking'])->name('customer.bookings.create');
        Route::post('/customer/bookings', [CustomerController::class, 'storeBooking'])->name('customer.bookings.store');
        Route::get('/customer/bookings/history', [CustomerController::class, 'bookingHistory'])->name('customer.bookings.history');
        
        Route::get('/customer/vehicles', [CustomerController::class, 'vehicles'])->name('customer.vehicles');
        Route::get('/customer/vehicles/create', [CustomerController::class, 'createVehicle'])->name('customer.vehicles.create');
        Route::post('/customer/vehicles', [CustomerController::class, 'storeVehicle'])->name('customer.vehicles.store');
        Route::get('/customer/vehicles/{vehicle}/edit', [CustomerController::class, 'editVehicle'])->name('customer.vehicles.edit');
        Route::put('/customer/vehicles/{vehicle}', [CustomerController::class, 'updateVehicle'])->name('customer.vehicles.update');
        
        Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile');
        Route::put('/customer/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    });

    // Common routes for all authenticated users
    // Appointments with custom routes
    Route::get('/appointments/today', [AppointmentController::class, 'today'])->name('appointments.today');
    Route::get('/appointments/track', [AppointmentController::class, 'track'])->name('appointments.track');
    Route::put('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');
    Route::resource('appointments', AppointmentController::class);

    // Customer feedback special routes - HARUS SEBELUM resource
    Route::get('/customer-feedback/testimonials', [CustomerFeedbackController::class, 'testimonials'])->name('customer-feedback.testimonials');
    Route::resource('customer-feedback', CustomerFeedbackController::class);

    // Payment special routes - jika ada
    Route::get('/payments/receipt/{payment}', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::resource('payments', PaymentController::class);

    Route::resource('services', ServiceController::class);
    Route::resource('service-schedules', ServiceScheduleController::class);
    Route::resource('vehicles', VehicleController::class);

    // Profile route
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');

    // Settings route
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // Notifications route
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    // Redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isMechanic()) {
            return redirect()->route('mechanic.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    })->name('dashboard');
});