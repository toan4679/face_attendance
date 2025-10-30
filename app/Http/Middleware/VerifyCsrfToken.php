<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Các route không cần kiểm tra CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*', // Cho phép API không cần token CSRF
    ];
}
