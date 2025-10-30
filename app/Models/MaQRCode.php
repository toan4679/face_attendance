<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaQRCode extends Model
{
    use HasFactory;

    protected $table = 'ma_qrcode';
    protected $primaryKey = 'qrcode_id';
    public $timestamps = true;

    protected $fillable = [
        'code_value',
        'thoi_gian_tao',
        'han_su_dung',
        'buoihoc_id',
    ];

    protected $casts = [
        'thoi_gian_tao' => 'datetime',
        'han_su_dung'   => 'datetime',
    ];

    // Quan hệ: mỗi mã QR thuộc 1 buổi học
    public function buoihoc()
    {
        return $this->belongsTo(BuoiHocKeHoach::class, 'buoihoc_id', 'buoihoc_id');
    }

    // Helper: đã hết hạn?
    public function getIsExpiredAttribute(): bool
    {
        return Carbon::now()->greaterThan($this->han_su_dung);
    }

    // Scopes tiện dùng
    public function scopeValid($query)
    {
        return $query->where('han_su_dung', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('han_su_dung', '<=', Carbon::now());
    }
}
