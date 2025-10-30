<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $table = 'khoa';
    protected $primaryKey = 'maKhoa';
    public $timestamps = true;

    protected $fillable = ['tenKhoa', 'moTa'];

    public function boMon()
    {
        return $this->hasMany(BoMon::class, 'maKhoa', 'maKhoa');
    }
}
