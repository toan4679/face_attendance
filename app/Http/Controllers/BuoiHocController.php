<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuoiHoc;
use Illuminate\Support\Str;

class BuoiHocController extends Controller
{
    /**
     * 📍 Lấy danh sách buổi học của giảng viên (theo mã GV)
     */
    public function getByGiangVien($maGV)
    {
        $buoiHocs = BuoiHoc::where('maGV', $maGV)
            ->with('lopHocPhan')
            ->orderBy('ngayHoc', 'desc')
            ->get();

        return response()->json($buoiHocs);
    }

      // Tạo mã QR
    public function generateQR(Request $request, $idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);

        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        // Tạo mã QR ngẫu nhiên
        $maQR = strtoupper(Str::random(8));
        $buoiHoc->maQR = $maQR;
        $buoiHoc->save();

        return response()->json([
            'message' => 'Tạo mã QR thành công',
            'maQR' => $maQR,
            'buoiHoc' => $buoiHoc,
        ]);
    }

    // Xóa mã QR
    public function clearQR($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::find($idBuoiHoc);

        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        $buoiHoc->maQR = null;
        $buoiHoc->save();

        return response()->json(['message' => 'Đã xóa mã QR thành công']);
    }

    // Lấy chi tiết buổi học
    public function getDetail($idBuoiHoc)
    {
        $buoiHoc = BuoiHoc::with(['giangVien', 'lopHocPhan'])->find($idBuoiHoc);

        if (!$buoiHoc) {
            return response()->json(['message' => 'Không tìm thấy buổi học'], 404);
        }

        return response()->json($buoiHoc);
    }
}
