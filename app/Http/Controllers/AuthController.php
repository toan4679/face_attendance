<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\PhongDaoTao;
use App\Models\GiangVien;
use App\Models\SinhVien;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản Giảng viên hoặc Sinh viên
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'loai' => 'required|in:giangvien,sinhvien',
            'hoTen' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('sinhvien', 'email'),
                Rule::unique('giangvien', 'email'),
            ],
            'matKhau' => 'required|string|min:6|confirmed',
        ]);

        $modelMap = [
            'giangvien' => GiangVien::class,
            'sinhvien'  => SinhVien::class,
        ];

        $model = $modelMap[$data['loai']];

        // Hash mật khẩu
        $hashedPassword = Hash::make($data['matKhau']);

        if ($data['loai'] === 'giangvien') {
            $user = $model::create([
                'hoTen' => $data['hoTen'],
                'email' => $data['email'],
                'matKhau' => $hashedPassword,
                'maBoMon' => null,
                'hocVi' => null,
                'soDienThoai' => null,
            ]);
        } else {
            $userData = [
                'maSo' => strtoupper('SV' . rand(1000, 9999)),
                'hoTen' => $data['hoTen'],
                'email' => $data['email'],
                'matKhau' => $hashedPassword,
            ];

            $user = $model::create($userData);
        }

        // Tạo token Sanctum
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => [
                'id' => $user->getKey(),
                'hoTen' => $user->hoTen,
                'email' => $user->email,
                'vaiTro' => $data['loai'],
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Đăng nhập cho 4 loại tài khoản
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'matKhau' => 'required|string',
        ]);

        // Danh sách model ứng với từng loại tài khoản
        $map = [
            'admin'     => Admin::class,
            'pdt'       => PhongDaoTao::class,
            'giangvien' => GiangVien::class,
            'sinhvien'  => SinhVien::class,
        ];

        $user = null;
        $role = null;

        // ✅ Tự động tìm xem email thuộc bảng nào
        foreach ($map as $key => $model) {
            $candidate = $model::where('email', $data['email'])->first();
            if ($candidate) {
                $user = $candidate;
                $role = $key;
                break;
            }
        }

        // ❌ Không tồn tại email
        if (!$user) {
            return response()->json([
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'Email không tồn tại trong hệ thống',
                ]
            ], 404);
        }

        // ❌ Sai mật khẩu
        if (!Hash::check($data['matKhau'], $user->matKhau)) {
            return response()->json([
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Mật khẩu không chính xác',
                ]
            ], 401);
        }

        // ✅ Tạo token Sanctum
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token'   => $token,
            'role'    => $role, // 👈 Thêm key này để Flutter dễ xử lý
            'user'    => [
                'id'     => $user->getKey(),
                'hoTen'  => $user->hoTen ?? ($user->name ?? null),
                'email'  => $user->email,
            ],
        ], 200);
    }


    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }

    /**
     * Làm mới token (refresh)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()?->currentAccessToken()?->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($data['old_password'], $user->matKhau)) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Mật khẩu cũ không đúng',
                ]
            ], 422);
        }

        $user->matKhau = Hash::make($data['new_password']);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công'], 200);
    }
}
