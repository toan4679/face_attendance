<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nganh extends Model
{
    use HasFactory;

    protected $table = 'nganh';
    protected $primaryKey = 'maNganh';
    public $timestamps = true;

    protected $fillable = ['maBoMon', 'tenNganh', 'maSo'];


    public function monHoc()
    {
        return $this->hasMany(MonHoc::class, 'maNganh', 'maNganh');
    }

    public function sinhVien()
    {
        return $this->hasMany(SinhVien::class, 'maNganh', 'maNganh');
    }
}
