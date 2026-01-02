<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FitnessMetricsController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutPlannerController;
use App\Http\Controllers\Api\WorkoutSessionController;
use App\Http\Controllers\Api\WorkoutTemplateController;
use Illuminate\Support\Facades\Route;

// Public authentication endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);

    // User endpoints
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/user/fitness-metrics', [FitnessMetricsController::class, 'index']);

    // Profile endpoints
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);

    // Exercises CRUD
    Route::apiResource('exercises', ExerciseController::class);

    // Plans CRUD
    Route::apiResource('plans', PlanController::class);

    // Workout Templates CRUD
    Route::apiResource('workout-templates', WorkoutTemplateController::class);

    // Workout Template Exercise Management
    Route::post('/workout-templates/{workoutTemplate}/exercises', [WorkoutTemplateController::class, 'addExercise']);
    Route::delete('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'removeExercise']);
    Route::put('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'updateExercise']);
    Route::post('/workout-templates/{workoutTemplate}/order', [WorkoutTemplateController::class, 'updateOrder']);

    // Weekly Workout Planner
    Route::get('/planner/weekly', [WorkoutPlannerController::class, 'index']);
    Route::post('/planner/assign', [WorkoutPlannerController::class, 'assign']);
    Route::post('/planner/unassign', [WorkoutPlannerController::class, 'unassign']);

    // Workout Sessions
    Route::get('/workout-sessions/calendar', [WorkoutSessionController::class, 'calendar']);
    Route::get('/workout-sessions/today', [WorkoutSessionController::class, 'today']);
    Route::post('/workout-sessions/start', [WorkoutSessionController::class, 'start']);
    Route::get('/workout-sessions/{session}', [WorkoutSessionController::class, 'show']);
    Route::post('/workout-sessions/{session}/complete', [WorkoutSessionController::class, 'complete']);
    Route::delete('/workout-sessions/{session}/cancel', [WorkoutSessionController::class, 'cancel']);

    // Workout Session Set Logs
    Route::post('/workout-sessions/{session}/sets', [WorkoutSessionController::class, 'logSet']);
    Route::put('/workout-sessions/{session}/sets/{setLog}', [WorkoutSessionController::class, 'updateSet']);
    Route::delete('/workout-sessions/{session}/sets/{setLog}', [WorkoutSessionController::class, 'deleteSet']);

    // Workout Session Exercise Management
    Route::post('/workout-sessions/{session}/exercises', [WorkoutSessionController::class, 'addExercise']);
    Route::delete('/workout-sessions/{session}/exercises/{exercise}', [WorkoutSessionController::class, 'removeExercise']);
    Route::put('/workout-sessions/{session}/exercises/{exercise}', [WorkoutSessionController::class, 'updateExercise']);
    Route::post('/workout-sessions/{session}/exercises/reorder', [WorkoutSessionController::class, 'reorderExercises']);
});
