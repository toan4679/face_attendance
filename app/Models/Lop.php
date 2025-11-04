<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    use HasFactory;

    protected $table = 'lop';
    protected $primaryKey = 'maLop';
    protected $fillable = [
        'maSoLop',
        'tenLop',
        'maNganh',
        'khoaHoc',
        'coVan',
    ];

    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'maNganh', 'maNganh');
    }
}
