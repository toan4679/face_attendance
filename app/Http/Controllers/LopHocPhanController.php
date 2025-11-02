<?php

namespace App\Http\Controllers;

use App\Models\LopHocPhan;
use Illuminate\Http\Request;

class LopHocPhanController extends Controller
{
    public function index()
    {
        return LopHocPhan::with(['monhoc', 'giangvien'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maMon' => 'required|exists:monhoc,maMon',
            'maGV' => 'required|exists:giangvien,maGV',
            'maSoLopHP' => 'required|string|max:20|unique:lophocphan,maSoLopHP',
            'hocKy' => 'required|string',
            'namHoc' => 'required|string',
            'thongTinLichHoc' => 'nullable|string',
        ]);

        $lop = LopHocPhan::create($data);
        return response()->json($lop, 201);
    }

    public function show($id)
    {
        return LopHocPhan::with(['monhoc', 'giangvien'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $lop = LopHocPhan::findOrFail($id);
        $lop->update($request->all());
        return response()->json($lop);
    }

    public function destroy($id)
    {
        LopHocPhan::destroy($id);
        return response()->json(['message' => 'Xóa lớp học phần thành công']);
    }
}
