<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccomplishmentPrintController;

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
