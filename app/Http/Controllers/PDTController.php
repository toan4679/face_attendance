<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Models\MonHoc;
use App\Models\LopHocPhan;
use App\Models\BuoiHoc;
use App\Models\KhuonMat;
use Illuminate\Http\Request;

class PDTController extends Controller
{
    /** Thống kê tổng quan cho dashboard */
    public function getDashboardStats()
    {
        return response()->json([
            'tongMonHoc' => MonHoc::count(),
            'tongLopHocPhan' => LopHocPhan::count(),
            'tongGiangVien' => GiangVien::count(),
            'tongSinhVien' => SinhVien::count(),
        ]);
    }

    /** Lấy toàn bộ sinh viên */
    public function getAllStudents()
    {
        return SinhVien::select('maSV', 'hoTen', 'email', 'maNganh')->get();
    }

    /** Lấy dữ liệu khuôn mặt sinh viên */
    public function getStudentFaces($id)
    {
        return KhuonMat::where('maSV', $id)->get(['maKhuonMat', 'duongDanAnh', 'created_at']);
    }

    /** Gán lịch dạy cho giảng viên */
    public function assignSchedule(Request $request)
    {
        $data = $request->validate([
            'maGV' => 'required|exists:giangvien,maGV',
            'maMon' => 'required|exists:monhoc,maMon',
            'hocKy' => 'required|string',
            'namHoc' => 'required|string',
            'maSoLopHP' => 'required|string',
            'thongTinLichHoc' => 'nullable|string',
        ]);

        $lop = LopHocPhan::create([
            'maMon' => $data['maMon'],
            'maGV' => $data['maGV'],
            'maSoLopHP' => $data['maSoLopHP'],
            'hocKy' => $data['hocKy'],
            'namHoc' => $data['namHoc'],
            'thongTinLichHoc' => $data['thongTinLichHoc'] ?? '',
        ]);

        return response()->json([
            'message' => 'Gán lịch dạy thành công',
            'lopHocPhan' => $lop,
        ], 201);
    }
}
