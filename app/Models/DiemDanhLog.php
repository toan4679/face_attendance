<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemDanhLog extends Model
{
    use HasFactory;
    protected $table = 'diemdanhlog';
    protected $primaryKey = 'diemdanh_id';
    protected $fillable = ['buoihoc_id', 'sinhvien_id', 'thoi_gian_diem_danh', 'trang_thai', 'confidence', 'image_capture_url'];

    public function buoihoc() {
        return $this->belongsTo(BuoiHocKeHoach::class, 'buoihoc_id', 'buoihoc_id');
    }

    public function sinhvien() {
        return $this->belongsTo(User::class, 'sinhvien_id', 'user_id');
    }
}
