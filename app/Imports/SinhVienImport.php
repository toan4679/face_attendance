<?php

namespace App\Imports;

use App\Models\SinhVien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class SinhVienImport implements ToModel, WithHeadingRow
{
    protected $maLop;

    public function __construct($maLop)
    {
        $this->maLop = $maLop;
    }

    public function model(array $row)
    {
         return new SinhVien([
        'maSo' => $row['ma_so'] ?? 'SV' . rand(1000, 9999),
        'hoTen' => $row['ho_ten'] ?? null,
        'email' => $row['email'] ?? null,
        'gioiTinh' => $row['gioi_tinh'] ?? null,
        'ngaySinh' => $row['ngay_sinh'] ?? null,
        'sdt' => $row['sdt'] ?? null,
        'diaChi' => $row['dia_chi'] ?? null,
        'maLop' => $this->maLop,
        // ✅ Thêm dòng dưới:
        'matKhau' => Hash::make('123456'), // hoặc tuỳ chọn mật khẩu mặc định
    ]);
    }
}
