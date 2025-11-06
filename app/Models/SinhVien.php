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
        'maNganh',
        'maLop',
        'maSo',
        'hoTen',
        'email',
        'matKhau',
        'soDienThoai',
        'khoaHoc',
        'anhDaiDien',
    ];

    protected $hidden = ['matKhau'];
    protected $casts = ['email_verified_at' => 'datetime'];

    // ✅ Quan hệ tới bảng nganh
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }

    // ✅ Quan hệ tới bảng lop
    public function lop()
    {
        return $this->belongsTo(Lop::class, 'maLop', 'maLop');
    }

    // ✅ Quan hệ khuôn mặt (1-1)
    public function khuonMat()
    {
        return $this->hasOne(KhuonMat::class, 'maSV', 'maSV');
    }

    // ✅ Quan hệ điểm danh
    public function diemDanh()
    {
        return $this->hasMany(DiemDanh::class, 'maSV', 'maSV');
    }
}
