<?php

namespace App\Http\Controllers;

use App\Models\BuoiHoc;
use Illuminate\Http\Request;

class BuoiHocController extends Controller
{
    public function index()
    {
        return BuoiHoc::with(['giangvien', 'lophocphan'])->get();
    }

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

    public function show($id)
    {
        return BuoiHoc::with(['giangvien', 'lophocphan'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $buoi = BuoiHoc::findOrFail($id);
        $buoi->update($request->all());
        return response()->json($buoi);
    }

    public function destroy($id)
    {
        BuoiHoc::destroy($id);
        return response()->json(['message' => 'Xóa buổi học thành công']);
    }
}
