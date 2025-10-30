<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class SinhVien extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'sinhvien';
    protected $primaryKey = 'maSV';
    public $timestamps = true;

    protected $fillable = [
        'maNganh', 'maLopHanhChinh', 'maSo', 'hoTen', 'email', 'matKhau',
        'soDienThoai', 'khoaHoc', 'anhDaiDien'
    ];

    protected $hidden = ['matKhau'];

    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }

    public function dangKyHoc()
    {
        return $this->hasMany(DangKyHoc::class, 'maSV', 'maSV');
    }

    public function lopHocPhan()
    {
        return $this->belongsToMany(LopHocPhan::class, 'dangkyhoc', 'maSV', 'maLopHP');
    }

    public function khuonMat()
    {
        return $this->hasOne(KhuonMat::class, 'maSV', 'maSV');
    }

    public function diemDanh()
    {
        return $this->hasMany(DiemDanh::class, 'maSV', 'maSV');
    }
}
