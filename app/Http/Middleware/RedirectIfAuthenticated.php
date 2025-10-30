<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Nếu người dùng đã đăng nhập, chuyển hướng họ.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if ($request->user()) {
            return redirect(RouteServiceProvider::HOME);
        }
        return $next($request);
    }
}
