<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHocPhan;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;

class LopHocPhanController extends Controller
{
    public function index()
    {
        $data = LopHocPhan::with(['monHoc', 'giangVien'])->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'maMon' => 'required|exists:monhoc,maMon',
                'maGV' => 'nullable|exists:giangvien,maGV',
                'maSoLopHP' => 'required|string|max:50',
                'hocKy' => 'required|string|max:20',
                'namHoc' => 'required|string|max:20',
                'ngayBatDau' => 'required|date',
                'ngayKetThuc' => 'required|date|after_or_equal:ngayBatDau',
                'thongTinLichHoc' => 'nullable|string|max:255',
                'dsMaLop' => 'nullable|array', // ✅ nhận mảng mã lớp
            ]);

            $lop = LopHocPhan::create($data);
            return response()->json($lop, 201);
        } catch (\Exception $e) {
            Log::error('❌ Lỗi thêm lớp học phần: ' . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi thêm lớp học phần',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $lop = LopHocPhan::findOrFail($id);

        $data = $request->validate([
            'maMon' => 'sometimes|exists:monhoc,maMon',
            'maGV' => 'nullable|exists:giangvien,maGV',
            'maSoLopHP' => 'sometimes|string|max:50',
            'hocKy' => 'sometimes|string|max:20',
            'namHoc' => 'sometimes|string|max:20',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'thongTinLichHoc' => 'nullable|string|max:255',
            'dsMaLop' => 'nullable|array',
        ]);

        $lop->update($data);
        return response()->json($lop);
    }

    public function show($id)
    {
        $lop = LopHocPhan::with(['monHoc', 'giangVien', 'buoiHoc'])->findOrFail($id);
        return response()->json($lop);
    }

    public function destroy($id)
    {
        LopHocPhan::destroy($id);
        return response()->json(['message' => 'Xóa lớp học phần thành công']);
    }

    /**
     * 🔍 Lấy danh sách sinh viên theo Lớp học phần
     */
    public function getSinhVienByLopHocPhan($maLopHP)
    {
        try {
            $lopHP = LopHocPhan::findOrFail($maLopHP);

            if (empty($lopHP->dsMaLop)) {
                return response()->json([
                    'message' => 'Lớp học phần chưa gắn lớp hành chính nào.',
                    'sinhVien' => [],
                ]);
            }

            // Lấy danh sách sinh viên từ nhiều lớp
            $sinhViens = SinhVien::whereIn('maLop', $lopHP->dsMaLop)
                ->select('maSV', 'maSo', 'hoTen', 'email', 'maLop', 'anhDaiDien')
                ->get();

            return response()->json([
                'lopHocPhan' => $lopHP->maSoLopHP,
                'dsMaLop' => $lopHP->dsMaLop,
                'tongSinhVien' => $sinhViens->count(),
                'sinhVien' => $sinhViens,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi lấy sinh viên lớp học phần: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi server khi lấy danh sách sinh viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 📘 Danh sách Lớp học phần do giảng viên phụ trách
     */
    public function byGiangVien(Request $request)
    {
        $user = $request->user();

        if (!$user || empty($user->maGV)) {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_USER',
                    'message' => 'Không xác định giảng viên hoặc chưa đăng nhập'
                ]
            ], 401);
        }

        try {
            $ds = LopHocPhan::with(['monHoc', 'giangVien'])
                ->where('maGV', $user->maGV)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'giangVien' => [
                    'maGV'  => $user->maGV,
                    'hoTen' => $user->hoTen ?? null,
                    'email' => $user->email ?? null,
                ],
                'count' => $ds->count(),
                'data'  => $ds,
            ]);
        } catch (\Throwable $e) {
            Log::error('byGiangVien error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Không thể tải danh sách lớp học phần',
                ]
            ], 500);
        }
    }
}
