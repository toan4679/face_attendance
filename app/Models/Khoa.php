<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;
    protected $table = 'khoa';
    protected $primaryKey = 'khoa_id';
    protected $fillable = ['ten_khoa'];

    // 1 Khoa có nhiều Bộ môn
    public function bomon() {
        return $this->hasMany(BoMon::class, 'khoa_id', 'khoa_id');
    }
}
