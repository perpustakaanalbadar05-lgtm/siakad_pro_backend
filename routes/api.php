<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AcademicYearController;
use App\Http\Controllers\Api\V1\BuildingController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CurriculumController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FacultyController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\StudyProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SIAKAD IAIMU
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // =====================================================================
    // AUTH MODULE - Public Routes
    // =====================================================================
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    });

    // =====================================================================
    // PROTECTED ROUTES (Requires Authentication)
    // =====================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/change-password', [AuthController::class, 'changePassword']);
        });

        // Dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('/admin', [DashboardController::class, 'superAdmin']);
            Route::get('/student', [DashboardController::class, 'student']);
            Route::get('/lecturer', [DashboardController::class, 'lecturer']);
        });

        // =====================================================================
        // MASTER DATA
        // =====================================================================

        // Faculties
        Route::apiResource('faculties', FacultyController::class);

        // Study Programs
        Route::apiResource('study-programs', StudyProgramController::class);

        // Academic Years
        Route::apiResource('academic-years', AcademicYearController::class);
        Route::patch('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate']);

        // Curriculums
        Route::apiResource('curriculums', CurriculumController::class);

        // Courses (Mata Kuliah)
        Route::apiResource('courses', CourseController::class);

        // Buildings & Rooms
        Route::apiResource('buildings', BuildingController::class);
        Route::apiResource('rooms', RoomController::class);

        // =====================================================================
        // MAHASISWA & DOSEN
        // =====================================================================

        Route::apiResource('students', StudentController::class);
        Route::apiResource('lecturers', LecturerController::class);
    });
});

// Health check
Route::get('/health', fn() => response()->json([
    'success' => true,
    'message' => 'SIAKAD IAIMU API is running.',
    'version' => 'v1.0',
    'timestamp' => now()->toIso8601String(),
]));
