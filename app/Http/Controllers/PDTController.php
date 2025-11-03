<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Models\LopHocPhan;
use App\Models\BuoiHoc;
use App\Helpers\RoleHelper;

class PDTController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Chưa đăng nhập'], 401);
        }

        $role = RoleHelper::getRole($user);
        if ($role !== 'pdt') {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }

        return response()->json([
            'tongMonHoc' => MonHoc::count(),
            'tongGiangVien' => GiangVien::count(),
            'tongSinhVien' => SinhVien::count(),
            'tongLopHocPhan' => LopHocPhan::count(),
        ]);
    }


    public function listMonHoc()
    {
        return response()->json(MonHoc::all());
    }

    public function createMonHoc(Request $request)
    {
        $data = $request->validate([
            'tenMonHoc' => 'required|string|max:100',
            'soTinChi' => 'required|integer',
        ]);
        $mon = MonHoc::create($data);
        return response()->json($mon, 201);
    }

    public function assignSchedule(Request $request)
    {
        try {
            $data = $request->validate([
                'maGV' => 'required|exists:giangvien,maGV',
                'maLopHP' => 'required|exists:lophocphan,maLopHP',
                'ngayHoc' => 'required|date',
                'gioBatDau' => 'required',
                'gioKetThuc' => 'required',
                'phongHoc' => 'required|string|max:50',
            ]);

            // Cập nhật giảng viên cho lớp học phần
            $lop = \App\Models\LopHocPhan::findOrFail($data['maLopHP']);
            $lop->maGV = $data['maGV'];
            $lop->save();

            // Tạo buổi học mới tương ứng
            $buoi = \App\Models\BuoiHoc::create([
                'maLopHP' => $data['maLopHP'],
                'maGV' => $data['maGV'],
                'ngayHoc' => $data['ngayHoc'],
                'gioBatDau' => $data['gioBatDau'],
                'gioKetThuc' => $data['gioKetThuc'],
                'phongHoc' => $data['phongHoc'],
                'maQR' => 'QR-' . uniqid(),
            ]);

            return response()->json([
                'message' => 'Gán lịch dạy thành công',
                'buoiHoc' => $buoi
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Lỗi server: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function createBuoiHoc(Request $request)
    {
        $data = $request->validate([
            'maLopHP' => 'required|exists:lophocphan,maLopHP',
            'maGV' => 'required|exists:giangvien,maGV',
            'ngayHoc' => 'required|date',
            'gioBatDau' => 'required',
            'gioKetThuc' => 'required',
            'phongHoc' => 'required|string',
        ]);
        $data['maQR'] = 'QR-' . uniqid();
        $buoi = BuoiHoc::create($data);
        return response()->json($buoi, 201);
    }
}
