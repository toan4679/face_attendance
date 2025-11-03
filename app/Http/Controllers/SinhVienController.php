<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;
use App\Models\BuoiHoc;
use App\Helpers\RoleHelper;
use App\Models\DiemDanh;

class SinhVienController extends Controller
{
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
