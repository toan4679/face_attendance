<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\DangKyHoc;
use App\Models\LopHocPhan;
use App\Models\BuoiHoc;
use App\Models\MonHoc;

class LichHocController extends Controller
{
    /**
     * Lấy danh sách lịch học của sinh viên đang đăng nhập
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Kiểm tra role sinh viên
        if (!($user instanceof SinhVien)) {
            return response()->json(['error' => 'Chỉ sinh viên mới được truy cập API này.'], 403);
        }

        // Lấy danh sách lớp học phần mà sinh viên đã đăng ký
        $lopDangKy = DangKyHoc::where('maSV', $user->maSV)
            ->pluck('maLopHP')
            ->toArray();

        // Lấy toàn bộ buổi học thuộc các lớp học phần đó
        $lichHoc = BuoiHoc::whereIn('maLopHP', $lopDangKy)
            ->with([
                'lophocphan.monhoc',
                'giangvien:maGV,hoTen,email',
            ])
            ->orderBy('ngayHoc', 'asc')
            ->get();

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'maSV' => $user->maSV,
            'hoTen' => $user->hoTen,
            'soBuoiHoc' => $lichHoc->count(),
            'lichHoc' => $lichHoc->map(function ($b) {
                return [
                    'maBuoi' => $b->maBuoi,
                    'ngayHoc' => $b->ngayHoc,
                    'gioBatDau' => $b->gioBatDau,
                    'gioKetThuc' => $b->gioKetThuc,
                    'phongHoc' => $b->phongHoc,
                    'maQR' => $b->maQR,
                    'tenMon' => $b->lophocphan->monhoc->tenMon ?? null,
                    'giangVien' => $b->giangvien->hoTen ?? null,
                    'hocKy' => $b->lophocphan->hocKy ?? null,
                    'namHoc' => $b->lophocphan->namHoc ?? null,
                ];
            }),
        ], 200);
    }
}
