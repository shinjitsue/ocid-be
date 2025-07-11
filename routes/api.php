<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\GraduateController;
use App\Http\Controllers\UndergradController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);
        Route::get('/activities', [AuthController::class, 'getUserActivities']);
        Route::get('/sessions', [AuthController::class, 'getSessions']);
        Route::delete('/sessions/{sessionId}', [AuthController::class, 'terminateSession']);
        Route::post('/sessions/extend', [AuthController::class, 'extendSession']);
    });

    // Resource routes for managing data
    Route::apiResource('campuses', CampusController::class);
    Route::apiResource('colleges', CollegeController::class);
    Route::apiResource('graduates', GraduateController::class);
    Route::apiResource('undergrads', UndergradController::class);
    Route::apiResource('curricula', CurriculumController::class);
    Route::apiResource('syllabi', SyllabusController::class);
    Route::apiResource('forms', FormController::class);
});
