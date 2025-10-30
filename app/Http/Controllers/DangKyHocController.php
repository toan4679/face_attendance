<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyHoc;

class DangKyHocController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'maLopHP' => 'required|exists:lophocphan,maLopHP',
            'maSV'    => 'required|exists:sinhvien,maSV'
        ]);
        $exists = DangKyHoc::where($data)->exists();
        if ($exists) {
            return response()->json(['error'=>['code'=>'CONFLICT','message'=>'Đã đăng ký']], 409);
        }
        $dk = DangKyHoc::create($data);
        return response()->json($dk, 201);
    }

    public function destroy($id)
    {
        DangKyHoc::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
