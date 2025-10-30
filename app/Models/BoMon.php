<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoMon extends Model
{
    use HasFactory;

    protected $table = 'bomon';
    protected $primaryKey = 'maBoMon';
    public $timestamps = true;

    protected $fillable = ['maKhoa', 'tenBoMon'];

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'maKhoa', 'maKhoa');
    }

    public function nganh()
    {
        return $this->hasMany(Nganh::class, 'maBoMon', 'maBoMon');
    }

    public function giangVien()
    {
        return $this->hasMany(GiangVien::class, 'maBoMon', 'maBoMon');
    }
}
