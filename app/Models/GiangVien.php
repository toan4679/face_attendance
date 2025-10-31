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
        'maBoMon',
        'hocVi',
        'soDienThoai',
    ];

    protected $hidden = ['matKhau'];

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'maBoMon', 'maBoMon');
    }

    public function lopHocPhan()
    {
        return $this->hasMany(LopHocPhan::class, 'maGV', 'maGV');
    }

    public function buoiHoc()
    {
        return $this->hasMany(BuoiHoc::class, 'maGV', 'maGV');
    }
}
