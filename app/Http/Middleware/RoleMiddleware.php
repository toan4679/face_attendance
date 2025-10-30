<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user(); // Sanctum tokenable (polymorphic)
        if (!$user) {
            return response()->json(['error' => ['code'=>'UNAUTHORIZED','message'=>'Chưa đăng nhập']], 401);
        }

        // map instance -> role name
        $map = [
            \App\Models\Admin::class       => 'admin',
            \App\Models\PhongDaoTao::class => 'pdt',
            \App\Models\GiangVien::class   => 'giangvien',
            \App\Models\SinhVien::class    => 'sinhvien',
        ];

        $role = $map[get_class($user)] ?? null;

        if (!$role || !in_array($role, $roles, true)) {
            return response()->json(['error'=>['code'=>'FORBIDDEN','message'=>'Không đủ quyền']], 403);
        }
        return $next($request);
    }
}
