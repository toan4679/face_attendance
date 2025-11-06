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
                'dsMaLop' => 'nullable|array',
            ]);

            if (isset($data['dsMaLop'])) {
                $data['dsMaLop'] = json_encode($data['dsMaLop']); // ✅ lưu mảng thành JSON
            }

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
        try {
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

            if (isset($data['dsMaLop'])) {
                $data['dsMaLop'] = json_encode($data['dsMaLop']);
            }

            $lop->update($data);
            return response()->json($lop);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi cập nhật lớp học phần: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi cập nhật lớp học phần',
                'error' => $e->getMessage(),
            ], 500);
        }
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

            // ✅ Chuẩn hóa dsMaLop
            $dsMaLop = $lopHP->dsMaLop ?? [];

            if (is_string($dsMaLop)) {
                // Nếu lưu dạng JSON "[1,2,3]"
                if (str_contains($dsMaLop, '[')) {
                    $dsMaLop = json_decode($dsMaLop, true);
                } else {
                    // Nếu lưu dạng "1,2,3"
                    $dsMaLop = array_filter(explode(',', $dsMaLop));
                }
            }

            if (!is_array($dsMaLop)) {
                $dsMaLop = [];
            }

            if (empty($dsMaLop)) {
                return response()->json([
                    'message' => 'Lớp học phần chưa gán lớp hành chính nào.',
                    'sinhVien' => [],
                    'dsMaLop' => [],
                ]);
            }

            // ✅ Lấy danh sách sinh viên theo nhiều lớp hành chính
            $sinhViens = SinhVien::whereIn('maLop', $dsMaLop)
                ->select('maSV', 'maSo', 'hoTen', 'email', 'maLop', 'anhDaiDien')
                ->get();

            return response()->json([
                'lopHocPhan' => $lopHP->maSoLopHP,
                'dsMaLop' => $dsMaLop,
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
            Log::error('byGiangVien error: ' . $e->getMessage());
            return response()->json([
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Không thể tải danh sách lớp học phần',
                ]
            ], 500);
        }
    }

    /**
     * 🧩 Gán lớp hành chính cho lớp học phần
     */
    public function ganLopHanhChinh(Request $request, $maLopHP)
    {
        try {
            $lopHP = LopHocPhan::findOrFail($maLopHP);

            $data = $request->validate([
                'dsMaLop' => 'required|array',
                'dsMaLop.*' => 'exists:lop,maLop',
            ]);

            $lopHP->dsMaLop = json_encode($data['dsMaLop']);
            $lopHP->save();

            return response()->json([
                'message' => '✅ Gán lớp hành chính thành công',
                'maLopHP' => $maLopHP,
                'dsMaLop' => $data['dsMaLop']
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi gán lớp hành chính: ' . $e->getMessage());
            return response()->json([
                'message' => 'Không thể gán lớp hành chính',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
