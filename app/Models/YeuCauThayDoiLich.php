<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauThayDoiLich extends Model
{
    use HasFactory;
    protected $table = 'yeucauthaydoilich';
    protected $primaryKey = 'yeucau_id';
    protected $fillable = ['loai_yeu_cau', 'ly_do', 'trang_thai_duyet', 'nguoi_yeu_cau_id', 'nguoi_day_thay_id', 'buoihoc_goc_id', 'buoihoc_bu_id'];

    public function nguoiYeuCau() {
        return $this->belongsTo(User::class, 'nguoi_yeu_cau_id', 'user_id');
    }

    public function nguoiDayThay() {
        return $this->belongsTo(User::class, 'nguoi_day_thay_id', 'user_id');
    }

    public function buoiHocGoc() {
        return $this->belongsTo(BuoiHocKeHoach::class, 'buoihoc_goc_id', 'buoihoc_id');
    }

    public function buoiHocBu() {
        return $this->belongsTo(BuoiHocKeHoach::class, 'buoihoc_bu_id', 'buoihoc_id');
    }
}
