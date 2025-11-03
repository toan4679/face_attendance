<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHocPhan;

class LopHocPhanController extends Controller
{
    public function index()
    {
        $data = LopHocPhan::with(['monHoc', 'giangVien'])->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maMon' => 'required|exists:monhoc,maMon',
            'maGV' => 'nullable|exists:giangvien,maGV',
            'maSoLopHP' => 'required|string|max:50',
            'hocKy' => 'required|string|max:20',
            'namHoc' => 'required|string|max:20',
            'ngayBatDau' => 'required|date',     // ✅ thêm
            'ngayKetThuc' => 'required|date|after_or_equal:ngayBatDau', // ✅ thêm
            'thongTinLichHoc' => 'nullable|string|max:255',
        ]);

        $lop = LopHocPhan::create($data);
        return response()->json($lop, 201);
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
}
