<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;
use App\Models\BuoiHoc;
use App\Helpers\RoleHelper;
use App\Models\DiemDanh;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;

class SinhVienController extends Controller
{

    public function index()
    {
        try {
            $sinhViens = SinhVien::with('lop', 'nganh')->get();

            return response()->json([
                'success' => true,
                'total' => $sinhViens->count(),
                'data' => $sinhViens,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách sinh viên: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sinh viên.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function dashboard(Request $request)
    {
        $role = RoleHelper::getRole($request->user());
        if ($role !== 'sinhvien') return response()->json(['error' => 'Không có quyền truy cập'], 403);

        $sv = $request->user();
        return response()->json([
            'tongMonDangKy' => DangKyHoc::where('maSV', $sv->maSV)->count(),
            'tongBuoiHoc' => DiemDanh::where('maSV', $sv->maSV)->count(),
        ]);
    }

    public function lichHoc(Request $request)
    {
        $sv = $request->user();
        $lich = DangKyHoc::with('lophocphan.buoihoc')
            ->where('maSV', $sv->maSV)
            ->get();
        return response()->json($lich);
    }
}
