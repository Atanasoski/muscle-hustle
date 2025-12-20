<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PartnerController;
use Illuminate\Support\Facades\Route;

// Public authentication endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // API Resources
    Route::apiResource('partners', PartnerController::class);
});
