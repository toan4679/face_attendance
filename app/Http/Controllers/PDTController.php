<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Models\LopHocPhan;
use App\Models\BuoiHoc;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Log; 

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
                'thu' => 'required|string|max:20',
                'tietBatDau' => 'required|integer|min:1',
                'tietKetThuc' => 'required|integer|min:1|gte:tietBatDau',
                'phongHoc' => 'nullable|string|max:50',
            ]);

            $buoi = \App\Models\BuoiHoc::create([
                'maGV' => $data['maGV'],
                'maLopHP' => $data['maLopHP'],
                'thu' => $data['thu'],
                'tietBatDau' => $data['tietBatDau'],
                'tietKetThuc' => $data['tietKetThuc'],
                'phongHoc' => $data['phongHoc'] ?? null,
            ]);

            return response()->json([
                'message' => 'Gán lịch dạy thành công',
                'data' => $buoi
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Lỗi gán lịch dạy: ' . $th->getMessage());
            return response()->json([
                'error' => 'Lỗi server: ' . $th->getMessage()
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
