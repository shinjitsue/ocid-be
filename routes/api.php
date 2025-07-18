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

// CSRF Cookie endpoint for SPA authentication
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware(['web']);

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
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    // Resource routes for managing data
    Route::apiResource('campuses', CampusController::class);
    Route::apiResource('graduates', GraduateController::class);
    Route::apiResource('undergrads', UndergradController::class);

    // College routes with file operations
    Route::apiResource('colleges', CollegeController::class);
    Route::post('colleges/{college}/upload-logo', [CollegeController::class, 'uploadLogo']);
    Route::delete('colleges/{college}/logo', [CollegeController::class, 'removeLogo']);

    // Curriculum routes with file operations
    Route::apiResource('curriculum', CurriculumController::class);
    Route::post('curriculum/{curriculum}/upload', [CurriculumController::class, 'uploadFile']);
    Route::delete('curriculum/{curriculum}/file', [CurriculumController::class, 'removeFile']);

    // Syllabus routes with file operations
    Route::apiResource('syllabus', SyllabusController::class);
    Route::post('syllabus/{syllabus}/upload', [SyllabusController::class, 'uploadFile']);
    Route::delete('syllabus/{syllabus}/file', [SyllabusController::class, 'removeFile']);

    // Form routes with file operations
    Route::apiResource('forms', FormController::class);
    Route::post('forms/{form}/upload', [FormController::class, 'uploadFile']);
    Route::delete('forms/{form}/file', [FormController::class, 'removeFile']);
});
