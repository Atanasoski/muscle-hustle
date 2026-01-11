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

    // Exercise Library
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::put('/exercises/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
