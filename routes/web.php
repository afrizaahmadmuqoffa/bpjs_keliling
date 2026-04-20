<?php

use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParticipantController;

// AUTH

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

//ADMIN & PIC

Route::middleware(['auth', 'role:admin,pic'])->group(function () {

    Route::prefix('participants')->group(function () {

        Route::get('/create', [ParticipantController::class, 'create'])
            ->name('participants.create');

        Route::get('/', [ParticipantController::class, 'index'])
            ->name('participants.index');

        Route::get('/{id}/edit', [ParticipantController::class, 'edit'])
            ->name('participants.edit');

        Route::get('/template', [ParticipantController::class, 'downloadTemplate'])
            ->name('participants.template');
    });
});


// ADMIN

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('dashboard')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'dashboard'])
            ->name('dashboard');
    });
});

// SUPER ADMIN

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::prefix('users')
        ->middleware(['auth', 'role:super_admin'])
        ->group(function () {

            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        });
});

// DEFAULT

Route::get('/', fn() => redirect('/login'));
