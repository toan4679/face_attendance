<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhongDaoTao;
use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Helpers\RoleHelper;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $role = RoleHelper::getRole($request->user());
        if ($role !== 'admin') return response()->json(['error' => 'Không có quyền truy cập'], 403);

        return response()->json([
            'tongPDT' => PhongDaoTao::count(),
            'tongGiangVien' => GiangVien::count(),
            'tongSinhVien' => SinhVien::count(),
        ]);
    }

    public function listPDT()
    {
        return response()->json(PhongDaoTao::all());
    }

    public function storePDT(Request $request)
    {
        $data = $request->validate([
            'hoTen' => 'required',
            'email' => 'required|email|unique:phongdaotao,email',
            'matKhau' => 'required|min:6',
        ]);
        $data['matKhau'] = bcrypt($data['matKhau']);
        $pdt = PhongDaoTao::create($data);
        return response()->json($pdt, 201);
    }

    public function resetPassword(Request $request, $id)
    {
        $pdt = PhongDaoTao::findOrFail($id);
        $pdt->matKhau = bcrypt('123456');
        $pdt->save();
        return response()->json(['message' => 'Đặt lại mật khẩu thành công']);
    }
}
