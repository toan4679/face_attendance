<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopHanhChinh extends Model
{
    use HasFactory;
    protected $table = 'lophanhchinh';
    protected $primaryKey = 'lophanhchinh_id';
    protected $fillable = ['ten_lop', 'nganh_id'];

    public function nganhDaoTao() {
        return $this->belongsTo(NganhDaoTao::class, 'nganh_id', 'nganh_id');
    }

    public function sinhvien() {
        return $this->hasMany(User::class, 'lophanhchinh_id', 'lophanhchinh_id');
    }
}
