<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $table = 'enrollments';
    protected $fillable = ['lophocphan_id', 'sinhvien_id', 'ty_le_nghi', 'trang_thai_hoc'];

    public function lophocphan() {
        return $this->belongsTo(LopHocPhan::class, 'lophocphan_id', 'lophocphan_id');
    }

    public function sinhvien() {
        return $this->belongsTo(User::class, 'sinhvien_id', 'user_id');
    }
}
