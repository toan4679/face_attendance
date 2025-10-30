<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopHocPhan extends Model
{
    use HasFactory;

    protected $table = 'lophocphan';
    protected $primaryKey = 'maLopHP';
    public $timestamps = true;

    protected $fillable = [
        'maMon', 'maGV', 'maSoLopHP', 'hocKy', 'namHoc', 'thongTinLichHoc'
    ];

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'maMon', 'maMon');
    }

    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'maGV', 'maGV');
    }

    public function buoiHoc()
    {
        return $this->hasMany(BuoiHoc::class, 'maLopHP', 'maLopHP');
    }

    public function sinhVien()
    {
        return $this->belongsToMany(SinhVien::class, 'dangkyhoc', 'maLopHP', 'maSV');
    }

    public function dangKyHoc()
    {
        return $this->hasMany(DangKyHoc::class, 'maLopHP', 'maLopHP');
    }
}
