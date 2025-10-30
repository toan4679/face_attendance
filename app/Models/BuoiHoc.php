<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuoiHoc extends Model
{
    use HasFactory;

    protected $table = 'buoihoc';
    protected $primaryKey = 'maBuoi';
    public $timestamps = true;

    protected $fillable = [
        'maLopHP', 'maGV', 'ngayHoc', 'gioBatDau', 'gioKetThuc', 'phongHoc', 'maQR'
    ];

    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'maLopHP', 'maLopHP');
    }

    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'maGV', 'maGV');
    }

    public function diemDanh()
    {
        return $this->hasMany(DiemDanh::class, 'maBuoi', 'maBuoi');
    }
}
