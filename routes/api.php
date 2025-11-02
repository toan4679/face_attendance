<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPDTController;
use App\Http\Controllers\KhoaController;
use App\Http\Controllers\BoMonController;
use App\Http\Controllers\NganhController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\GiangVienController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\LopHocPhanController;
use App\Http\Controllers\DangKyHocController;
use App\Http\Controllers\BuoiHocController;
use App\Http\Controllers\KhuonMatController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\LichDayController;
use App\Http\Controllers\LichHocController;
use App\Http\Controllers\PDTController;

Route::get('/test', fn() => response()->json(['ok' => true]));

Route::prefix('v1')->group(function () {

    /* ========== AUTHENTICATION ========== */
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/change-password', [AuthController::class, 'changePassword']);
    });

    /* ========== ADMIN ========== */
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::apiResource('admin/pdt', AdminPDTController::class)->except(['show']);
        Route::post('admin/reset-password', [AdminPDTController::class, 'resetPassword']);
    });

    /* ========== PHÒNG ĐÀO TẠO ========== */
    Route::middleware(['auth:sanctum', 'role:pdt'])->prefix('pdt')->group(function () {
        // Dashboard
        Route::get('dashboard/stats', [PDTController::class, 'getDashboardStats']);
        Route::post('gan-lich', [PDTController::class, 'assignSchedule']);

        // Quản lý danh mục
        Route::apiResource('khoa', KhoaController::class);
        Route::apiResource('bomon', BoMonController::class);
        Route::apiResource('nganh', NganhController::class);
        Route::apiResource('monhoc', MonHocController::class);

        // Quản lý nhân sự và học viên
        Route::apiResource('giangvien', GiangVienController::class);
        Route::apiResource('sinhvien', SinhVienController::class);

        // Lớp học phần & buổi học
        Route::apiResource('lophocphan', LopHocPhanController::class);
        Route::apiResource('buoihoc', BuoiHocController::class);

        // Đăng ký học
        Route::post('dangkyhoc', [DangKyHocController::class, 'store']);
        Route::delete('dangkyhoc/{id}', [DangKyHocController::class, 'destroy']);

        // Khuôn mặt sinh viên (duyệt)
        Route::get('khuonmat/pending', [KhuonMatController::class, 'pending']);
        Route::post('khuonmat/{id}/approve', [KhuonMatController::class, 'approve']);
        Route::post('khuonmat/{id}/reject', [KhuonMatController::class, 'reject']);

        // Thông báo
        Route::apiResource('thongbao', ThongBaoController::class)->only(['index', 'store', 'destroy']);
    });

    /* ========== GIẢNG VIÊN ========== */
    Route::middleware(['auth:sanctum', 'role:giangvien'])->prefix('giangvien')->group(function () {
        Route::get('lichday', [LichDayController::class, 'index']);
        Route::get('lophocphan', [LopHocPhanController::class, 'byGiangVien']);

        Route::post('buoihoc/{maBuoi}/qr', [QRController::class, 'generate']);
        Route::post('buoihoc/{maBuoi}/close', [QRController::class, 'close']);

        Route::get('buoihoc/{maBuoi}/diemdanh', [CheckInController::class, 'listByBuoi']);
        Route::patch('diemdanh/{id}', [CheckInController::class, 'updateStatus']);
    });

    /* ========== SINH VIÊN ========== */
    Route::middleware(['auth:sanctum', 'role:sinhvien'])->prefix('sinhvien')->group(function () {
        Route::get('lichhoc', [LichHocController::class, 'index']);
        Route::get('diemdanh', [CheckInController::class, 'history']);

        // Dữ liệu khuôn mặt
        Route::post('khuonmat', [KhuonMatController::class, 'store']);
        Route::get('khuonmat', [KhuonMatController::class, 'showMine']);
        Route::delete('khuonmat/{id}', [KhuonMatController::class, 'destroyMine']);

        // Điểm danh
        Route::post('attendance/check-in/qr', [CheckInController::class, 'checkInQR']);
        Route::post('attendance/check-in/face', [CheckInController::class, 'checkInFace']);
    });

    /* ========== PUBLIC TEST (Không cần token) ========== */
    Route::get('buoihoc/giangvien/{maGV}', [BuoiHocController::class, 'getByGiangVien']);
});
