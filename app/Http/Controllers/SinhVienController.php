<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SinhVienController extends BaseCrudController
{
    protected $model = SinhVien::class;
    protected $searchable = ['hoTen','email','maSo','maLopHanhChinh'];
    protected $rulesCreate = [
        'maNganh' => 'required|exists:nganh,maNganh',
        'maLopHanhChinh' => 'nullable|string|max:20',
        'maSo'    => 'required|string|max:20|unique:sinhvien,maSo',
        'hoTen'   => 'required|string|max:100',
        'email'   => 'required|email|unique:sinhvien,email',
        'matKhau' => 'required|min:6',
        'soDienThoai'=>'nullable|string|max:20',
        'khoaHoc' => 'nullable|integer',
        'anhDaiDien'=> 'nullable|string|max:255',
    ];

    public function store(Request $request)
    {
        $data = $request->validate($this->rulesCreate);
        $data['matKhau'] = Hash::make($data['matKhau']);
        $sv = SinhVien::create($data);
        return response()->json($sv, 201);
    }

    public function update(Request $request, $id)
    {
        $sv   = SinhVien::findOrFail($id);
        $data = $request->validate([
            'maNganh' => 'sometimes|exists:nganh,maNganh',
            'maLopHanhChinh' => 'nullable|string|max:20',
            'maSo'    => 'sometimes|string|max:20|unique:sinhvien,maSo,'.$sv->maSV.',maSV',
            'hoTen'   => 'sometimes|string|max:100',
            'email'   => 'sometimes|email|unique:sinhvien,email,'.$sv->maSV.',maSV',
            'matKhau' => 'nullable|min:6',
            'soDienThoai'=>'nullable|string|max:20',
            'khoaHoc' => 'nullable|integer',
            'anhDaiDien'=> 'nullable|string|max:255',
        ]);
        if (!empty($data['matKhau'])) $data['matKhau'] = Hash::make($data['matKhau']);
        $sv->update($data);
        return response()->json($sv);
    }
}
