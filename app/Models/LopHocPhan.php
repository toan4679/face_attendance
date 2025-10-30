<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopHocPhan extends Model
{
    use HasFactory;
    protected $table = 'lophocphan';
    protected $primaryKey = 'lophocphan_id';
    protected $fillable = ['ten_LHP', 'max_sv', 'loai', 'hocphan_id', 'giaidoan_id', 'giangvien_chinh_id', 'parent_lhp_id'];

    public function hocphan() {
        return $this->belongsTo(HocPhan::class, 'hocphan_id', 'hocphan_id');
    }

    public function giangvien() {
        return $this->belongsTo(User::class, 'giangvien_chinh_id', 'user_id');
    }

    public function buoihoc() {
        return $this->hasMany(BuoiHocKeHoach::class, 'lophocphan_id', 'lophocphan_id');
    }

    public function sinhvien() {
        return $this->belongsToMany(User::class, 'enrollments', 'lophocphan_id', 'sinhvien_id')
                    ->withPivot(['ty_le_nghi', 'trang_thai_hoc'])->withTimestamps();
    }
}
