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
use App\Http\Controllers\LopController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\LichDayController;
use App\Http\Controllers\LichHocController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Tất cả API sẽ được prefix bằng /api/v1/...
| Ví dụ:
|   POST /api/v1/auth/login
|   GET  /api/v1/pdt/dashboard/stats
|--------------------------------------------------------------------------
*/

Route::get('/test', fn() => response()->json(['ok' => true]));

/*
|--------------------------------------------------------------------------
| PREFIX: /api/v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    | Đăng nhập, đăng ký, đăng xuất, đổi mật khẩu
    */
    Route::prefix('auth')->group(function () {
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
    | Quản lý tài khoản Phòng Đào Tạo, reset mật khẩu
    */
    Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
        Route::get('/pdt', [AdminController::class, 'index']);
        Route::post('/pdt', [AdminController::class, 'store']);
        Route::patch('/pdt/{id}', [AdminController::class, 'update']);
        Route::delete('/pdt/{id}', [AdminController::class, 'destroy']);
        Route::post('/reset-password', [AdminController::class, 'resetPassword']);
    });

    /*
    |--------------------------------------------------------------------------
    | PHÒNG ĐÀO TẠO (PDT)
    |--------------------------------------------------------------------------
    | Quản lý Khoa, Bộ môn, Ngành, Môn học, Giảng viên, Sinh viên, Lớp học phần...
    */
    Route::prefix('pdt')->middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard/stats', [PDTController::class, 'dashboard']);

        // CRUD quản lý danh mục
        Route::apiResource('khoa', KhoaController::class);
        Route::apiResource('nganh', NganhController::class);
        Route::apiResource('monhoc', MonHocController::class);

        // CRUD quản lý giảng viên, sinh viên, lớp học phần
        Route::apiResource('giangvien', GiangVienController::class);
        Route::apiResource('sinhvien', SinhVienController::class);
        Route::apiResource('lophocphan', LopHocPhanController::class);
        Route::apiResource('lop', LopController::class);

        // CRUD buổi học
        Route::apiResource('buoihoc', BuoiHocController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        // Gán lịch giảng dạy
        Route::post('/schedule/assign', [PDTController::class, 'assignSchedule']);

        // Quản lý khuôn mặt sinh viên
        Route::get('/khuonmat/pending', [KhuonMatController::class, 'pending']);
        Route::post('/khuonmat/{id}/approve', [KhuonMatController::class, 'approve']);
        Route::post('/khuonmat/{id}/reject', [KhuonMatController::class, 'reject']);

        // Quản lý thông báo
        Route::get('/thongbao', [ThongBaoController::class, 'index']);
        Route::post('/thongbao', [ThongBaoController::class, 'store']);
        Route::delete('/thongbao/{id}', [ThongBaoController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | GIẢNG VIÊN
    |--------------------------------------------------------------------------
    | Quản lý điểm danh, QR code, xem lịch dạy
    */
    Route::prefix('giangvien')->middleware('auth:sanctum')->group(function () {
        Route::get('/lichday', [LichDayController::class, 'index']);
        Route::get('/lophocphan', [LopHocPhanController::class, 'byGiangVien']);

        // QR điểm danh
        Route::post('/buoihoc/{maBuoi}/qr', [QRController::class, 'generate']);
        Route::post('/buoihoc/{maBuoi}/close', [QRController::class, 'close']);

        // Điểm danh thủ công
        Route::get('/buoihoc/{maBuoi}/diemdanh', [DiemDanhController::class, 'listByBuoi']);
        Route::patch('/diemdanh/{id}', [DiemDanhController::class, 'updateStatus']);
    });

    /*
    |--------------------------------------------------------------------------
    | SINH VIÊN
    |--------------------------------------------------------------------------
    | Xem lịch học, điểm danh (face/QR), quản lý khuôn mặt
    */
    Route::prefix('sinhvien')->middleware('auth:sanctum')->group(function () {
        Route::get('/lichhoc', [LichHocController::class, 'index']);
        Route::get('/diemdanh', [DiemDanhController::class, 'history']);

        // Khuôn mặt sinh viên
        Route::post('/khuonmat', [KhuonMatController::class, 'store']);
        Route::get('/khuonmat', [KhuonMatController::class, 'showMine']);
        Route::delete('/khuonmat/{id}', [KhuonMatController::class, 'destroyMine']);

        // Check-in điểm danh
        Route::post('/attendance/check-in/qr', [DiemDanhController::class, 'checkInQR']);
        Route::post('/attendance/check-in/face', [DiemDanhController::class, 'checkInFace']);
    });
});
