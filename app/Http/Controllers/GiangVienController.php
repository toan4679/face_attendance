<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GiangVienController extends BaseCrudController
{
    protected $model = GiangVien::class;
    protected $searchable = ['hoTen','email','soDienThoai'];
    protected $rulesCreate = [
        'maBoMon' => 'required|exists:bomon,maBoMon',
        'hoTen'   => 'required|string|max:100',
        'email'   => 'required|email|unique:giangvien,email',
        'matKhau' => 'required|min:6',
        'soDienThoai' => 'nullable|string|max:20',
        'hocVi'   => 'nullable|string|max:50'
    ];

    public function store(Request $request)
    {
        $data = $request->validate($this->rulesCreate);
        $data['matKhau'] = Hash::make($data['matKhau']);
        $gv = GiangVien::create($data);
        return response()->json($gv, 201);
    }

    public function update(Request $request, $id)
    {
        $gv   = GiangVien::findOrFail($id);
        $data = $request->validate([
            'maBoMon' => 'sometimes|exists:bomon,maBoMon',
            'hoTen'   => 'sometimes|string|max:100',
            'email'   => 'sometimes|email|unique:giangvien,email,'.$gv->maGV.',maGV',
            'matKhau' => 'nullable|min:6',
            'soDienThoai' => 'nullable|string|max:20',
            'hocVi'   => 'nullable|string|max:50'
        ]);
        if (!empty($data['matKhau'])) $data['matKhau'] = Hash::make($data['matKhau']);
        $gv->update($data);
        return response()->json($gv);
    }
}
