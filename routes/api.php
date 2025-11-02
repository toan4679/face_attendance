<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPDTController;
use App\Http\Controllers\PDTController;
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

Route::get('/test', fn() => response()->json(['ok' => true]));

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
| Đăng nhập, đăng ký, đổi mật khẩu, refresh token
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
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/pdt', [AdminPDTController::class, 'store']);
    Route::get('/pdt', [AdminPDTController::class, 'index']);
    Route::patch('/pdt/{id}', [AdminPDTController::class, 'update']);
    Route::delete('/pdt/{id}', [AdminPDTController::class, 'destroy']);
    Route::post('/reset-password', [AdminPDTController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| PHÒNG ĐÀO TẠO (PĐT)
|--------------------------------------------------------------------------
| Quản lý Khoa, Bộ môn, Ngành, Môn học, Giảng viên, Sinh viên, Lớp học phần, ...
*/
Route::prefix('v1/pdt')->middleware(['auth:sanctum', 'role:pdt'])->group(function () {
    // Dashboard thống kê
    Route::get('/dashboard/stats', [PDTController::class, 'getDashboardStats']);

    // Quản lý danh mục
    Route::apiResource('khoa', KhoaController::class);
    Route::apiResource('bomon', BoMonController::class);
    Route::apiResource('nganh', NganhController::class);
    Route::apiResource('monhoc', MonHocController::class);

    // Quản lý giảng viên, sinh viên, lớp học phần
    Route::apiResource('giangvien', GiangVienController::class);
    Route::apiResource('sinhvien', SinhVienController::class);
    Route::apiResource('lophocphan', LopHocPhanController::class);

    // Buổi học
    Route::apiResource('buoihoc', BuoiHocController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Gán lịch dạy cho giảng viên
    Route::post('/schedule/assign', [PDTController::class, 'assignSchedule']);

    // Quản lý khuôn mặt sinh viên (duyệt, từ chối)
    Route::get('/khuonmat/pending', [KhuonMatController::class, 'pending']);
    Route::post('/khuonmat/{id}/approve', [KhuonMatController::class, 'approve']);
    Route::post('/khuonmat/{id}/reject', [KhuonMatController::class, 'reject']);

    // Thông báo
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
Route::prefix('v1/giangvien')->middleware(['auth:sanctum', 'role:giangvien'])->group(function () {
    Route::get('/lichday', [LichDayController::class, 'index']);
    Route::get('/lophocphan', [LopHocPhanController::class, 'byGiangVien']);

    // QR điểm danh
    Route::post('/buoihoc/{maBuoi}/qr', [QRController::class, 'generate']);
    Route::post('/buoihoc/{maBuoi}/close', [QRController::class, 'close']);

    // Điểm danh sinh viên
    Route::get('/buoihoc/{maBuoi}/diemdanh', [CheckInController::class, 'listByBuoi']);
    Route::patch('/diemdanh/{id}', [CheckInController::class, 'updateStatus']);
});

/*
|--------------------------------------------------------------------------
| SINH VIÊN
|--------------------------------------------------------------------------
| Theo dõi lịch học, điểm danh bằng khuôn mặt hoặc QR
*/
Route::prefix('v1/sinhvien')->middleware(['auth:sanctum', 'role:sinhvien'])->group(function () {
    Route::get('/lichhoc', [LichHocController::class, 'index']);
    Route::get('/diemdanh', [CheckInController::class, 'history']);

    // Quản lý dữ liệu khuôn mặt
    Route::post('/khuonmat', [KhuonMatController::class, 'store']);
    Route::get('/khuonmat', [KhuonMatController::class, 'showMine']);
    Route::delete('/khuonmat/{id}', [KhuonMatController::class, 'destroyMine']);

    // Điểm danh
    Route::post('/attendance/check-in/qr', [CheckInController::class, 'checkInQR']);
    Route::post('/attendance/check-in/face', [CheckInController::class, 'checkInFace']);
});
