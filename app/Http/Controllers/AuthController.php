<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\PhongDaoTao;
use App\Models\GiangVien;
use App\Models\SinhVien;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'loai' => 'required|in:giangvien,sinhvien',
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|unique:sinhvien,email|unique:giangvien,email',
            'matKhau' => 'required|string|min:6|confirmed',
            'soDienThoai' => 'nullable|string|max:20',
            'maSo' => 'nullable|string|max:20', // cho sinh viên
            'maNganh' => 'nullable|integer|exists:nganh,maNganh',
            'maBoMon' => 'nullable|integer|exists:bomon,maBoMon',
            'hocVi' => 'nullable|string|max:50'
        ]);

        $modelMap = [
            'giangvien' => \App\Models\GiangVien::class,
            'sinhvien'  => \App\Models\SinhVien::class,
        ];

        $model = $modelMap[$data['loai']];

        // Hash password
        $data['matKhau'] = \Illuminate\Support\Facades\Hash::make($data['matKhau']);

        // Chuẩn hóa field theo loại
        if ($data['loai'] === 'giangvien') {
            $user = $model::create([
                'hoTen' => $data['hoTen'],
                'email' => $data['email'],
                'matKhau' => $data['matKhau'],
                'maBoMon' => $data['maBoMon'],
                'hocVi' => $data['hocVi'] ?? null,
                'soDienThoai' => $data['soDienThoai'] ?? null,
            ]);
        } else { // sinhvien
            $user = $model::create([
                'maSo' => $data['maSo'] ?? strtoupper('SV' . rand(1000, 9999)),
                'hoTen' => $data['hoTen'],
                'email' => $data['email'],
                'matKhau' => $data['matKhau'],
                'maNganh' => $data['maNganh'],
                'soDienThoai' => $data['soDienThoai'] ?? null,
            ]);
        }

        // Tạo token tự động
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => [
                'id' => $user->getKey(),
                'hoTen' => $user->hoTen,
                'email' => $user->email,
                'vaiTro' => $data['loai']
            ],
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'loai' => 'required|in:admin,pdt,giangvien,sinhvien'
        ]);

        $map = [
            'admin'     => Admin::class,
            'pdt'       => PhongDaoTao::class,
            'giangvien' => GiangVien::class,
            'sinhvien'  => SinhVien::class,
        ];

        $model = $map[$data['loai']];
        $user  = $model::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->matKhau)) {
            return response()->json(['error' => ['code' => 'UNAUTHORIZED', 'message' => 'Email hoặc mật khẩu không đúng']], 401);
        }
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id' => $user->getKey(),
                'hoTen' => $user->hoTen ?? ($user->name ?? null),
                'vaiTro' => $data['loai']
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(null, 204);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()?->currentAccessToken()?->delete();
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);
        $user = $request->user();
        if (!\Illuminate\Support\Facades\Hash::check($data['old_password'], $user->matKhau)) {
            return response()->json(['error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Mật khẩu cũ không đúng']], 422);
        }
        $user->matKhau = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        $user->save();
        return response()->json(null, 204);
    }
}
