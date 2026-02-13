<?php

use App\Http\Controllers\Api\AccomplishmentDetailController;
use App\Http\Controllers\Api\AccomplishmentHeaderController;
use App\Http\Controllers\Api\AccomplishmentPrintController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// // Public routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // Protected routes (Sanctum token required)
// Route::middleware('auth:sanctum')->group(function () {
//     // Get logged-in user info
//     Route::get('/user', function () {
//         return auth()->user();
//     });

//     // Print a specific accomplishment
//     Route::get('/accomplishments/{id}/print', [AccomplishmentPrintController::class, 'print']);

//     // Logout current token
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

// Public routes



// Route::get('accomplishments/print', [AccomplishmentPrintController::class, 'print'])
//     ->name('api.accomplishments.print');

// //http://172.31.102.215:8000/accomplishment-headers?department_id=26
// Accomplishment Headers
Route::get('accomplishment-headers', [AccomplishmentHeaderController::class, 'index']);
// Route::get('accomplishment-headers/{id}', [AccomplishmentHeaderController::class, 'show']);

// //http://172.31.102.215:8000/accomplishment-details?header_id=10
// Accomplishment Details
Route::get('accomplishment-details', [AccomplishmentDetailController::class, 'index']);
// Route::get('accomplishment-details/{id}', [AccomplishmentDetailController::class, 'show']);
