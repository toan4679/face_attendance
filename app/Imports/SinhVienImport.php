<?php

namespace App\Imports;

use App\Models\SinhVien;
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
        return new SinhVien([
            'maSV' => $row['ma_sv'] ?? null,
            'hoTen' => $row['ho_ten'] ?? null,
            'email' => $row['email'] ?? null,
            'gioiTinh' => $row['gioi_tinh'] ?? null,
            'ngaySinh' => $row['ngay_sinh'] ?? null,
            'maLop' => $this->maLop,
        ]);
    }
}
