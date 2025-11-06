<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHocPhan;
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
     * Lấy danh sách Lớp học phần do giảng viên (đang đăng nhập) phụ trách
     * Route: GET /api/v1/giangvien/lophocphan
     */
    public function byGiangVien(Request $request)
    {
        $user = $request->user();

        // Bảo vệ: chưa đăng nhập hoặc tài khoản không phải giảng viên
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
            Log::error('byGiangVien error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Không thể tải danh sách lớp học phần',
                ]
            ], 500);
        }
    }
}
