<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocPhan extends Model
{
    use HasFactory;
    protected $table = 'hocphan';
    protected $primaryKey = 'hocphan_id';
    protected $fillable = ['ten_hoc_phan', 'so_tin_chi', 'so_tiet_LT', 'so_tiet_TH', 'bomon_id'];

    public function bomon() {
        return $this->belongsTo(BoMon::class, 'bomon_id', 'bomon_id');
    }

    public function lophocphan() {
        return $this->hasMany(LopHocPhan::class, 'hocphan_id', 'hocphan_id');
    }
}
