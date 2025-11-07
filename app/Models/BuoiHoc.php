<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuoiHoc extends Model
{
    use HasFactory;

    protected $table = 'buoihoc';
    protected $primaryKey = 'maBuoi';
    public $timestamps = true; // có cột created_at và updated_at

    protected $fillable = [
        'maLopHP',
        'maGV',
        'thu',
        'tietBatDau',
        'tietKetThuc',
        'ngayHoc',
        'gioBatDau',
        'gioKetThuc',
        'phongHoc',
        'maQR',
    ];

    // Quan hệ với bảng giảng viên
    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'maGV', 'maGV');
    }

    // Quan hệ với bảng lớp học phần
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'maLopHP', 'maLopHP');
    }
}
