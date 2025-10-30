<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyHoc extends Model
{
    use HasFactory;

    protected $table = 'dangkyhoc';
    protected $primaryKey = 'maDangKy';
    public $timestamps = true;

    protected $fillable = ['maLopHP', 'maSV'];

    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'maLopHP', 'maLopHP');
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
