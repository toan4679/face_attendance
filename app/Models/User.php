<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['email', 'password', 'ho_ten', 'role', 'bomon_id', 'lophanhchinh_id', 'ma_sv'];

    protected $hidden = ['password', 'remember_token'];

    public function bomon() {
        return $this->belongsTo(BoMon::class, 'bomon_id', 'bomon_id');
    }

    public function lophanhchinh() {
        return $this->belongsTo(LopHanhChinh::class, 'lophanhchinh_id', 'lophanhchinh_id');
    }

    public function face() {
        return $this->hasOne(Face::class, 'user_id', 'user_id');
    }

    public function lopHocPhanGiangDay() {
        return $this->hasMany(LopHocPhan::class, 'giangvien_chinh_id', 'user_id');
    }

    public function enrollment() {
        return $this->belongsToMany(LopHocPhan::class, 'enrollments', 'sinhvien_id', 'lophocphan_id')
                    ->withPivot(['ty_le_nghi', 'trang_thai_hoc'])->withTimestamps();
    }
}
