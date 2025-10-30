<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'monhoc';
    protected $primaryKey = 'maMon';
    public $timestamps = true;

    protected $fillable = ['maNganh', 'maSoMon', 'tenMon', 'soTinChi', 'moTa'];

    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }

    public function lopHocPhan()
    {
        return $this->hasMany(LopHocPhan::class, 'maMon', 'maMon');
    }
}
