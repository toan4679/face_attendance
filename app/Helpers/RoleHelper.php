<?php
namespace App\Helpers;

use App\Models\Admin;
use App\Models\PhongDaoTao;
use App\Models\GiangVien;
use App\Models\SinhVien;

class RoleHelper
{
    public static function getRole($user)
    {
        return match (get_class($user)) {
            Admin::class => 'admin',
            PhongDaoTao::class => 'pdt',
            GiangVien::class => 'giangvien',
            SinhVien::class => 'sinhvien',
            default => 'unknown',
        };
    }
}
