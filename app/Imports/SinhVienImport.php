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
        // ✅ Lấy email từ file (nếu trống thì bỏ qua)
        $email = $row['email'] ?? null;
        if (!$email) {
            return null;
        }

        // 🔍 Nếu sinh viên này đã tồn tại (theo email), thì cập nhật lại lớp học
        $existing = SinhVien::where('email', $email)->first();

        if ($existing) {
            $existing->update([
                'maLop'   => $this->maLop,
                'hoTen'   => $row['ho_ten'] ?? $existing->hoTen,
                'gioiTinh'=> $row['gioi_tinh'] ?? $existing->gioiTinh,
                'ngaySinh'=> $row['ngay_sinh'] ?? $existing->ngaySinh,
                'sdt'     => $row['sdt'] ?? $existing->sdt,
                'diaChi'  => $row['dia_chi'] ?? $existing->diaChi,
            ]);

            return null; // ⚠️ Không tạo mới (chỉ cập nhật)
        }

        // ➕ Nếu chưa có thì thêm mới
        return new SinhVien([
            'maSo'     => $row['ma_so'] ?? 'SV' . rand(1000, 9999),
            'hoTen'    => $row['ho_ten'] ?? null,
            'email'    => $email,
            'gioiTinh' => $row['gioi_tinh'] ?? null,
            'ngaySinh' => $row['ngay_sinh'] ?? null,
            'sdt'      => $row['sdt'] ?? null,
            'diaChi'   => $row['dia_chi'] ?? null,
            'maLop'    => $this->maLop,
            'matKhau'  => Hash::make('123456'),
        ]);
    }
}
