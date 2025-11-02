<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SinhVienController extends Controller
{
    public function index()
    {
        return SinhVien::with('nganh')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maSo' => 'required|string|unique:sinhvien,maSo',
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:sinhvien,email',
            'matKhau' => 'required|string|min:6',
            'maNganh' => 'required|exists:nganh,maNganh',
            'soDienThoai' => 'nullable|string|max:15',
        ]);

        $data['matKhau'] = Hash::make($data['matKhau']);
        $sinhVien = SinhVien::create($data);
        return response()->json($sinhVien, 201);
    }

    public function show($id)
    {
        return SinhVien::with('nganh')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $sv = SinhVien::findOrFail($id);
        if ($request->has('matKhau')) {
            $request['matKhau'] = Hash::make($request['matKhau']);
        }
        $sv->update($request->all());
        return response()->json($sv);
    }

    public function destroy($id)
    {
        SinhVien::destroy($id);
        return response()->json(['message' => 'Xóa sinh viên thành công']);
    }
}
