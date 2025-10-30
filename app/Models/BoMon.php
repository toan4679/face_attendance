<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoMon extends Model
{
    use HasFactory;
    protected $table = 'bomon';
    protected $primaryKey = 'bomon_id';
    protected $fillable = ['ten_bo_mon', 'khoa_id'];

    public function khoa() {
        return $this->belongsTo(Khoa::class, 'khoa_id', 'khoa_id');
    }

    public function nganhDaoTao() {
        return $this->hasMany(NganhDaoTao::class, 'bomon_id', 'bomon_id');
    }

    public function hocphan() {
        return $this->hasMany(HocPhan::class, 'bomon_id', 'bomon_id');
    }

    public function giangvien() {
        return $this->hasMany(User::class, 'bomon_id', 'bomon_id');
    }
}
