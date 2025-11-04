<?php

namespace App\Imports;

use App\Models\SinhVien;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SinhVienImport implements ToModel, WithHeadingRow
{
    protected $maLop;

    public function __construct($maLop)
    {
        $this->maLop = $maLop;
    }

    public function model(array $row)
    {
        // ⚙️ Các cột trong file Excel cần khớp tên header: ma_sv, ho_ten, email, gioi_tinh, khoa_hoc
        return new SinhVien([
            'maSV' => $row['ma_sv'],
            'hoTen' => $row['ho_ten'],
            'email' => $row['email'],
            'gioiTinh' => $row['gioi_tinh'],
            'khoaHoc' => $row['khoa_hoc'],
            'maLop' => $this->maLop,
            'password' => Hash::make('123456'), // đặt mật khẩu mặc định
        ]);
    }
}
