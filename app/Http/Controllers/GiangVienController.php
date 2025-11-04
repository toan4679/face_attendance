<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use Illuminate\Http\Request;

class GiangVienController extends Controller
{
    public function index()
    {
        $data = GiangVien::all();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:giangvien,email',
            'matKhau' => 'required|string|min:6',
            'soDienThoai' => 'nullable|string|max:20',
            'hocVi' => 'nullable|string|max:50',
        ]);

        $validated['matKhau'] = bcrypt($validated['matKhau']);
        $giangVien = GiangVien::create($validated);

        return response()->json(['message' => 'Thêm giảng viên thành công', 'data' => $giangVien]);
    }

    public function show($id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }
        return response()->json(['data' => $gv]);
    }

    public function update(Request $request, $id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }

        $gv->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $gv]);
    }

    public function destroy($id)
    {
        $gv = GiangVien::find($id);
        if (!$gv) {
            return response()->json(['error' => 'Không tìm thấy giảng viên'], 404);
        }

        $gv->delete();
        return response()->json(['message' => 'Xóa thành công']);
    }
}
