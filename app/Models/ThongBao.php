<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thongbao';
    protected $primaryKey = 'maThongBao';
    public $timestamps = true;

    protected $fillable = [
        'tieuDe', 'noiDung', 'nguoiGui', 'nguoiNhanLoai', 'maNguoiNhan'
    ];
}
