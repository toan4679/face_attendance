<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PDTController;
use App\Http\Controllers\KhoaController;
use App\Http\Controllers\BoMonController;
use App\Http\Controllers\NganhController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\GiangVienController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\LopHocPhanController;
use App\Http\Controllers\BuoiHocController;
use App\Http\Controllers\KhuonMatController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\LichDayController;
use App\Http\Controllers\LichHocController;

Route::get('/test', fn() => response()->json(['ok' => true]));

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
| Quản lý tài khoản PĐT, reset mật khẩu, ...
*/
Route::prefix('v1/admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/pdt', [AdminController::class, 'index']);
    Route::post('/pdt', [AdminController::class, 'store']);
    Route::patch('/pdt/{id}', [AdminController::class, 'update']);
    Route::delete('/pdt/{id}', [AdminController::class, 'destroy']);
    Route::post('/reset-password', [AdminController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| PHÒNG ĐÀO TẠO
|--------------------------------------------------------------------------
| Quản lý Khoa, Bộ môn, Ngành, Môn học, Giảng viên, Sinh viên, ...
*/
Route::prefix('v1/pdt')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [PDTController::class, 'dashboard']);

    Route::apiResource('khoa', KhoaController::class);
    Route::apiResource('bomon', BoMonController::class);
    Route::apiResource('nganh', NganhController::class);
    Route::apiResource('monhoc', MonHocController::class);

    Route::apiResource('giangvien', GiangVienController::class);
    Route::apiResource('sinhvien', SinhVienController::class);
    Route::apiResource('lophocphan', LopHocPhanController::class);

    Route::apiResource('buoihoc', BuoiHocController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('/schedule/assign', [PDTController::class, 'assignSchedule']);

    Route::get('/khuonmat/pending', [KhuonMatController::class, 'pending']);
    Route::post('/khuonmat/{id}/approve', [KhuonMatController::class, 'approve']);
    Route::post('/khuonmat/{id}/reject', [KhuonMatController::class, 'reject']);

    Route::get('/thongbao', [ThongBaoController::class, 'index']);
    Route::post('/thongbao', [ThongBaoController::class, 'store']);
    Route::delete('/thongbao/{id}', [ThongBaoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| GIẢNG VIÊN
|--------------------------------------------------------------------------
| Quản lý điểm danh, QR, xem lịch dạy, xuất báo cáo
*/
Route::prefix('v1/giangvien')->middleware('auth:sanctum')->group(function () {
    Route::get('/lichday', [LichDayController::class, 'index']);
    Route::get('/lophocphan', [LopHocPhanController::class, 'byGiangVien']);

    Route::post('/buoihoc/{maBuoi}/qr', [QRController::class, 'generate']);
    Route::post('/buoihoc/{maBuoi}/close', [QRController::class, 'close']);

    Route::get('/buoihoc/{maBuoi}/diemdanh', [DiemDanhController::class, 'listByBuoi']);
    Route::patch('/diemdanh/{id}', [DiemDanhController::class, 'updateStatus']);
});

/*
|--------------------------------------------------------------------------
| SINH VIÊN
|--------------------------------------------------------------------------
| Theo dõi lịch học, điểm danh bằng khuôn mặt hoặc QR
*/
Route::prefix('v1/sinhvien')->middleware('auth:sanctum')->group(function () {
    Route::get('/lichhoc', [LichHocController::class, 'index']);
    Route::get('/diemdanh', [DiemDanhController::class, 'history']);

    Route::post('/khuonmat', [KhuonMatController::class, 'store']);
    Route::get('/khuonmat', [KhuonMatController::class, 'showMine']);
    Route::delete('/khuonmat/{id}', [KhuonMatController::class, 'destroyMine']);

    Route::post('/attendance/check-in/qr', [DiemDanhController::class, 'checkInQR']);
    Route::post('/attendance/check-in/face', [DiemDanhController::class, 'checkInFace']);
});
