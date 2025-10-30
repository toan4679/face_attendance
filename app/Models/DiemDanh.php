<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemDanh extends Model
{
    use HasFactory;

    protected $table = 'diemdanh';
    protected $primaryKey = 'maDiemDanh';
    public $timestamps = true;

    protected $fillable = [
        'maBuoi', 'maSV', 'trangThai', 'thoiGianDiemDanh', 'hinhThuc', 'xacThucKhuonMat'
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }

    public function buoiHoc()
    {
        return $this->belongsTo(BuoiHoc::class, 'maBuoi', 'maBuoi');
    }
}
