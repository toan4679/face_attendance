<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NganhDaoTao extends Model
{
    use HasFactory;
    protected $table = 'nganhdaotao';
    protected $primaryKey = 'nganh_id';
    protected $fillable = ['ten_nganh', 'bomon_id'];

    public function bomon() {
        return $this->belongsTo(BoMon::class, 'bomon_id', 'bomon_id');
    }

    public function lopHanhChinh() {
        return $this->hasMany(LopHanhChinh::class, 'nganh_id', 'nganh_id');
    }
}
