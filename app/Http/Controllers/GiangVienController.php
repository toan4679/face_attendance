<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GiangVienController extends Controller
{
    public function index()
    {
        return GiangVien::with('bomon')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:giangvien,email',
            'matKhau' => 'required|string|min:6',
            'maBoMon' => 'nullable|exists:bomon,maBoMon',
            'hocVi' => 'nullable|string|max:50',
            'soDienThoai' => 'nullable|string|max:15',
        ]);

        $data['matKhau'] = Hash::make($data['matKhau']);
        $gv = GiangVien::create($data);
        return response()->json($gv, 201);
    }

    public function show($id)
    {
        return GiangVien::with('bomon')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $gv = GiangVien::findOrFail($id);
        if ($request->has('matKhau')) {
            $request['matKhau'] = Hash::make($request['matKhau']);
        }
        $gv->update($request->all());
        return response()->json($gv);
    }

    public function destroy($id)
    {
        GiangVien::destroy($id);
        return response()->json(['message' => 'Xóa giảng viên thành công']);
    }
}
