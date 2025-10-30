<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuoiHocKeHoach extends Model
{
    use HasFactory;
    protected $table = 'buoihockehoach';
    protected $primaryKey = 'buoihoc_id';
    protected $fillable = ['lophocphan_id', 'phonghoc_id', 'thoi_gian_bat_dau', 'so_tiet', 'trang_thai', 'noi_dung_giang_day', 'giangvien_day_id'];

    public function lophocphan() {
        return $this->belongsTo(LopHocPhan::class, 'lophocphan_id', 'lophocphan_id');
    }

    public function giangvien() {
        return $this->belongsTo(User::class, 'giangvien_day_id', 'user_id');
    }

    public function diemdanh() {
        return $this->hasMany(DiemDanhLog::class, 'buoihoc_id', 'buoihoc_id');
    }
}
