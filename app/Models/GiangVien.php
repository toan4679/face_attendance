<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class GiangVien extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'giangvien';
    protected $primaryKey = 'maGV';
    public $timestamps = true;

    protected $fillable = [
        'hoTen',
        'email',
        'matKhau',
        'hocVi',
        'soDienThoai',
    ];

    protected $hidden = ['matKhau'];



    public function lopHocPhan()
    {
        return $this->hasMany(LopHocPhan::class, 'maGV', 'maGV');
    }

    public function buoiHoc()
    {
        return $this->hasMany(BuoiHoc::class, 'maGV', 'maGV');
    }
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'maKhoa', 'maKhoa');
    }
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }
}
