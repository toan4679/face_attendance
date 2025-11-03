<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;

class BuoiHocController extends Controller
{
    // ✅ Lấy danh sách buổi học (mặc định lấy toàn bộ, có thể giới hạn qua query)
    public function index(Request $request)
    {
        $limit = $request->query('limit'); // vd: /api/v1/pdt/buoihoc?limit=5

        $query = BuoiHoc::with(['giangvien', 'lophocphan.monHoc'])
            ->orderBy('ngayHoc', 'desc')
            ->orderBy('gioBatDau', 'desc');

        if ($limit) {
            $query->limit((int) $limit);
        }

        return response()->json($query->get());
    }

    // ✅ Thêm mới buổi học
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP' => 'required|exists:lophocphan,maLopHP',
            'maGV' => 'required|exists:giangvien,maGV',
            'ngayHoc' => 'required|date',
            'gioBatDau' => 'required',
            'gioKetThuc' => 'required',
            'phongHoc' => 'nullable|string|max:50',
            'maQR' => 'nullable|string|max:255',
        ]);

        $buoi = BuoiHoc::create($data);
        return response()->json($buoi, 201);
    }

    // ✅ Xem chi tiết 1 buổi học
    public function show($id)
    {
        return BuoiHoc::with(['giangvien', 'lophocphan.monHoc'])->findOrFail($id);
    }

    // ✅ Cập nhật
    public function update(Request $request, $id)
    {
        $buoi = BuoiHoc::findOrFail($id);
        $buoi->update($request->all());
        return response()->json($buoi);
    }

    // ✅ Xóa
    public function destroy($id)
    {
        BuoiHoc::destroy($id);
        return response()->json(['message' => 'Xóa buổi học thành công']);
    }
}
