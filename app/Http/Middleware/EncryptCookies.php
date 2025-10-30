<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Các cookie không cần mã hoá.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
