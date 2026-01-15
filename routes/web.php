<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Exercise Library - Admin routes (prefixed with /admin, no conflict with API routes since API routes use 'api.' prefix)
    Route::prefix('admin')->group(function () {
        Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
        Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
        Route::put('/exercises/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
        Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
    });

    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Partner Management
    Route::resource('partners', \App\Http\Controllers\PartnerController::class);

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // User Invitations Management
    Route::get('/user-invitations', [UserController::class, 'invitationsIndex'])->name('user-invitations.index');
    Route::post('/user-invitations/invite', [UserController::class, 'invitationsStore'])->name('user-invitations.invite');
    Route::post('/user-invitations/{invitation}/resend', [UserController::class, 'invitationsResend'])->name('user-invitations.resend');
    Route::delete('/user-invitations/{invitation}', [UserController::class, 'invitationsDestroy'])->name('user-invitations.destroy');

    // Exercise Library - Partner routes
    Route::get('/partner/exercises', [ExerciseController::class, 'partnerIndex'])->name('partner.exercises.index');
    Route::get('/partner/exercises/{exercise}', [ExerciseController::class, 'show'])->name('partner.exercises.show');
    Route::get('/partner/exercises/{exercise}/edit', [ExerciseController::class, 'edit'])->name('partner.exercises.edit');
    Route::put('/exercises/{exercise}/partner', [ExerciseController::class, 'updatePartnerExercises'])->name('exercises.updatePartner');
    Route::post('/partner/exercises/bulk-link', [ExerciseController::class, 'bulkLink'])->name('partner.exercises.bulkLink');
    Route::post('/exercises/{exercise}/link', [ExerciseController::class, 'linkExercise'])->name('exercises.link');
    Route::post('/exercises/{exercise}/unlink', [ExerciseController::class, 'unlinkExercise'])->name('exercises.unlink');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
