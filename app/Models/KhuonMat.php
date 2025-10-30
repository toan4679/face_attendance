<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuonMat extends Model
{
    use HasFactory;

    protected $table = 'khuonmat';
    protected $primaryKey = 'maKhuonMat';
    public $timestamps = true;

    protected $fillable = ['maSV', 'duongDanAnh', 'duLieuNhanDien'];

    protected $casts = [
        'duLieuNhanDien' => 'array'
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'maSV', 'maSV');
    }
}
