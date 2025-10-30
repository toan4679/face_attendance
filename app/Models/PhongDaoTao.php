<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class PhongDaoTao extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'phongdaotao';
    protected $primaryKey = 'maPDT';
    public $timestamps = true;

    protected $fillable = ['maAdmin', 'hoTen', 'email', 'matKhau', 'soDienThoai'];

    protected $hidden = ['matKhau'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'maAdmin', 'maAdmin');
    }
}
