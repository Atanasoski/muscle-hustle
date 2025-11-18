<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MealPlannerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkoutPlannerController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\WorkoutTemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Workout Templates CRUD
    Route::resource('workout-templates', WorkoutTemplateController::class);

    // Workout Template Exercise Management
    Route::post('/workout-templates/{workoutTemplate}/exercises', [WorkoutTemplateController::class, 'addExercise'])
        ->name('workout-templates.add-exercise');
    Route::delete('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'removeExercise'])
        ->name('workout-templates.remove-exercise');
    Route::put('/workout-templates/{workoutTemplate}/exercises/{exercise}', [WorkoutTemplateController::class, 'updateExercise'])
        ->name('workout-templates.update-exercise');
    Route::post('/workout-templates/{workoutTemplate}/order', [WorkoutTemplateController::class, 'updateOrder'])
        ->name('workout-templates.update-order');

    // Weekly Workout Planner
    Route::get('/planner/workouts', [WorkoutPlannerController::class, 'index'])->name('planner.workouts');
    Route::post('/planner/workouts/assign', [WorkoutPlannerController::class, 'assign'])->name('planner.workouts.assign');
    Route::post('/planner/workouts/unassign', [WorkoutPlannerController::class, 'unassign'])->name('planner.workouts.unassign');

    // Weekly Meal Planner
    Route::get('/planner/meals', [MealPlannerController::class, 'index'])->name('planner.meals');
    Route::post('/planner/meals', [MealPlannerController::class, 'store'])->name('planner.meals.store');
    Route::delete('/planner/meals/{meal}', [MealPlannerController::class, 'destroy'])->name('planner.meals.destroy');

    // Workout Sessions
    Route::get('/workouts/today', [WorkoutSessionController::class, 'today'])->name('workouts.today');
    Route::post('/workouts/start', [WorkoutSessionController::class, 'start'])->name('workouts.start');
    Route::get('/workouts/{session}', [WorkoutSessionController::class, 'show'])->name('workouts.session');
    Route::post('/workouts/{session}/log-set', [WorkoutSessionController::class, 'logSet'])->name('workouts.log-set');
    Route::post('/workouts/{session}/complete', [WorkoutSessionController::class, 'complete'])->name('workouts.complete');
    Route::delete('/workouts/{session}/cancel', [WorkoutSessionController::class, 'cancel'])->name('workouts.cancel');
    Route::delete('/workouts/{session}/sets/{setLog}', [WorkoutSessionController::class, 'deleteSet'])->name('workouts.delete-set');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
