<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    use HasFactory;

    protected $table = 'lop'; // nếu bảng trong DB là tbLop, đổi lại
    protected $primaryKey = 'maLop';
    public $timestamps = true;

    protected $fillable = [
        'maSoLop',
        'tenLop',
        'maNganh',
        'khoaHoc',
        'coVan',
    ];

    // ✅ Quan hệ 1-nhiều tới SinhVien
    public function sinhviens()
    {
        return $this->hasMany(SinhVien::class, 'maLop', 'maLop');
    }

    // ✅ Quan hệ ngược lại tới Nganh (nếu có)
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }
}
