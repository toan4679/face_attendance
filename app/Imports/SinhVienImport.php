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
            'maSo' => $row['ma_so'] ?? null, 
            'hoTen' => $row['ho_ten'] ?? null,
            'email' => $row['email'] ?? null,
            'maLop' => $this->maLop,
        ]);
    }
}
