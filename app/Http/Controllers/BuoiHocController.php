<?php

namespace App\Http\Controllers;

use App\Models\Buoihoc;
use Illuminate\Http\Request;

class BuoihocController extends Controller
{
    // ✅ Thêm lịch dạy mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP' => 'required|exists:lophocphan,maLopHP',
            'maGV' => 'required|exists:giangvien,maGV',
            'ngayHoc' => 'required|date',
            'gioBatDau' => 'required|date_format:H:i',
            'gioKetThuc' => 'required|date_format:H:i|after:gioBatDau',
            'phongHoc' => 'nullable|string|max:50',
        ]);

        $data['maQR'] = 'QR' . strtoupper(uniqid());
        $buoi = Buoihoc::create($data);

        return response()->json([
            'message' => 'Tạo lịch dạy thành công',
            'buoiHoc' => $buoi
        ], 201);
    }

    // ✅ Lấy danh sách lịch dạy của một giảng viên
    public function getByGiangVien($maGV)
    {
        $lichDay = Buoihoc::where('maGV', $maGV)
            ->with(['lopHocPhan'])
            ->orderBy('ngayHoc', 'asc')
            ->get();

        if ($lichDay->isEmpty()) {
            return response()->json(['message' => 'Không có lịch dạy'], 404);
        }

        return response()->json($lichDay, 200);
    }
}
