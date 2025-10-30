<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, UserController, FaceController,
    AttendanceController, ClassController,
    ScheduleController, QRCodeController, ReportController
};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::post('/faces/store', [FaceController::class, 'store']);
    Route::post('/faces/verify', [FaceController::class, 'verify']);

    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn']);
    Route::get('/attendance/history/{student_id}', [AttendanceController::class, 'history']);

    Route::get('/lophocphan', [ClassController::class, 'index']);
    Route::get('/lophocphan/{id}', [ClassController::class, 'show']);

    Route::get('/buoihoc', [ScheduleController::class, 'index']);
    Route::get('/buoihoc/{id}', [ScheduleController::class, 'show']);

    Route::post('/qrcode/generate/{buoihoc_id}', [QRCodeController::class, 'generate']);
    Route::post('/qrcode/verify', [QRCodeController::class, 'verify']);

    Route::get('/report/summary', [ReportController::class, 'summary']);
});
