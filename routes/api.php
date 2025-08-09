<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\PartController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController; // ADD: Import ServiceController
use App\Http\Controllers\Api\ServiceScheduleController; // ADD: Import ServiceScheduleController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // API v1 routes
    Route::prefix('v1')->group(function () {
        
        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::prefix('admin')->group(function () {
                Route::apiResource('users', UserController::class);
                
                // Parts management (admin only)
                Route::get('parts/low-stock', [PartController::class, 'lowStock']);
                Route::get('parts/search', [PartController::class, 'search']);
                Route::post('parts/{part}/adjust-stock', [PartController::class, 'adjustStock']);
                Route::get('parts/{part}/transactions', [PartController::class, 'getTransactions']);
                Route::apiResource('parts', PartController::class);
                
                // Service Categories management (admin only)
                Route::get('service-categories/search', [ServiceCategoryController::class, 'search']);
                Route::get('service-categories/{serviceCategory}/statistics', [ServiceCategoryController::class, 'statistics']);
                Route::apiResource('service-categories', ServiceCategoryController::class);

                // TAMBAHAN: Services management (admin only - full CRUD)
                Route::get('services/search', [ServiceController::class, 'search']);
                Route::get('services/popular', [ServiceController::class, 'popular']);
                Route::get('services/{service}/statistics', [ServiceController::class, 'statistics']);
                Route::get('services/category/{categoryId}', [ServiceController::class, 'byCategory']);
                Route::apiResource('services', ServiceController::class);

                // TAMBAHAN: Service Schedules management (admin only - full CRUD)
                Route::get('service-schedules/today', [ServiceScheduleController::class, 'today']);
                Route::get('service-schedules/available-slots', [ServiceScheduleController::class, 'availableSlots']);
                Route::get('service-schedules/operational-hours', [ServiceScheduleController::class, 'operationalHours']);
                Route::get('service-schedules/day/{dayOfWeek}', [ServiceScheduleController::class, 'byDay']);
                Route::apiResource('service-schedules', ServiceScheduleController::class);
            });
        });
        
        // Routes accessible by admin and mechanic
        Route::middleware('role:admin,mechanic')->group(function () {
            // Work order management routes
            // Route::apiResource('work-orders', WorkOrderController::class);
            
            // Vehicle management (admin/mechanic can CRUD all vehicles)
            Route::put('vehicles/{vehicle}', [VehicleController::class, 'update']);
            Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy']);
            
            // Parts read access for mechanics (for work orders)
            Route::get('parts', [PartController::class, 'index']);
            Route::get('parts/search', [PartController::class, 'search']);
            Route::get('parts/{part}', [PartController::class, 'show']);
            Route::get('parts/{part}/transactions', [PartController::class, 'getTransactions']);
            
            // Service Categories read access for mechanics
            Route::get('service-categories', [ServiceCategoryController::class, 'index']);
            Route::get('service-categories/all', [ServiceCategoryController::class, 'all']);
            Route::get('service-categories/{serviceCategory}', [ServiceCategoryController::class, 'show']);
            Route::get('service-categories/{serviceCategory}/with-services', [ServiceCategoryController::class, 'withServices']);

            // TAMBAHAN: Services read access for mechanics (for work orders)
            Route::get('services', [ServiceController::class, 'index']);
            Route::get('services/all', [ServiceController::class, 'all']);
            Route::get('services/search', [ServiceController::class, 'search']);
            Route::get('services/popular', [ServiceController::class, 'popular']);
            Route::get('services/{service}', [ServiceController::class, 'show']);
            Route::get('services/category/{categoryId}', [ServiceController::class, 'byCategory']);

            // TAMBAHAN: Service Schedules read access for mechanics (for planning work orders)
            Route::get('service-schedules', [ServiceScheduleController::class, 'index']);
            Route::get('service-schedules/all', [ServiceScheduleController::class, 'all']);
            Route::get('service-schedules/today', [ServiceScheduleController::class, 'today']);
            Route::get('service-schedules/available-slots', [ServiceScheduleController::class, 'availableSlots']);
            Route::get('service-schedules/operational-hours', [ServiceScheduleController::class, 'operationalHours']);
            Route::get('service-schedules/day/{dayOfWeek}', [ServiceScheduleController::class, 'byDay']);
            Route::get('service-schedules/{serviceSchedule}', [ServiceScheduleController::class, 'show']);
        });
        
        // Routes accessible by all authenticated users
        Route::group([], function () {
            // Vehicle routes accessible by all users
            Route::get('vehicles', [VehicleController::class, 'index']); // List vehicles (filtered by role)
            Route::post('vehicles', [VehicleController::class, 'store']); // Create vehicle
            Route::get('vehicles/search', [VehicleController::class, 'search']); // Search vehicles
            Route::get('vehicles/by-customer', [VehicleController::class, 'getByCustomer']); // Get vehicles by customer
            Route::get('vehicles/{vehicle}', [VehicleController::class, 'show']); // Show single vehicle
            Route::get('vehicles/{vehicle}/details', [VehicleController::class, 'getDetails']); // Get vehicle details with customer info
            
            // Service Categories read-only access for customers (for booking)
            Route::get('service-categories/all', [ServiceCategoryController::class, 'all']); // For dropdowns
            Route::get('service-categories/{serviceCategory}/with-services', [ServiceCategoryController::class, 'withServices']); // For service selection

            // TAMBAHAN: Services read-only access for customers (for booking)
            Route::get('services/all', [ServiceController::class, 'all']); // For dropdowns/selection
            Route::get('services/popular', [ServiceController::class, 'popular']); // Popular services for customers
            Route::get('services/search', [ServiceController::class, 'search']); // Search services for booking
            Route::get('services/category/{categoryId}', [ServiceController::class, 'byCategory']); // Services by category for customers

            // TAMBAHAN: Service Schedules read-only access for customers (for booking)
            Route::get('service-schedules/all', [ServiceScheduleController::class, 'all']); // All schedules for booking
            Route::get('service-schedules/today', [ServiceScheduleController::class, 'today']); // Today's schedule
            Route::get('service-schedules/available-slots', [ServiceScheduleController::class, 'availableSlots']); // Available time slots for booking
            Route::get('service-schedules/operational-hours', [ServiceScheduleController::class, 'operationalHours']); // Operational hours for customers
            Route::get('service-schedules/day/{dayOfWeek}', [ServiceScheduleController::class, 'byDay']); // Schedule for specific day
            
            // Profile routes
            // Route::get('/profile', [ProfileController::class, 'show']);
            // Route::put('/profile', [ProfileController::class, 'update']);
            
            // Customer routes
            // Route::apiResource('appointments', AppointmentController::class);
        });
    });
});