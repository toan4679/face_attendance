<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuoiHoc extends Model
{
    use HasFactory;

    protected $table = 'buoihoc';
    protected $primaryKey = 'maBuoi';
    protected $fillable = [
        'maLopHP',
        'maGV',
        'thu',          // ✅ thêm
        'tietBatDau',   // ✅ thêm
        'tietKetThuc',  // ✅ thêm
        'phongHoc',
        'maQR'
    ];

    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'maGV', 'maGV');
    }

    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'maLopHP', 'maLopHP');
    }
}
