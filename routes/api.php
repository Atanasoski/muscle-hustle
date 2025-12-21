<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\WorkoutTemplateController;
use Illuminate\Support\Facades\Route;

// Public authentication endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Exercises CRUD
    Route::apiResource('exercises', ExerciseController::class);

    // Workout Templates CRUD
    Route::apiResource('workout-templates', WorkoutTemplateController::class);

    // Workout Template Exercise Management
    Route::post('/workout-templates/{workoutTemplate}/exercises', [WorkoutTemplateController::class, 'addExercise']);
    Route::delete('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'removeExercise']);
    Route::put('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'updateExercise']);
    Route::post('/workout-templates/{workoutTemplate}/order', [WorkoutTemplateController::class, 'updateOrder']);
});
