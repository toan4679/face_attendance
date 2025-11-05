<?php

namespace App\Imports;

use App\Models\SinhVien;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;


class SinhVienImport implements ToModel, WithHeadingRow
{
    protected $maLop;

    public function __construct($maLop)
    {
        $this->maLop = $maLop;
    }

    public function model(array $row)
    {
        // Lấy mã sinh viên (nếu không có thì tạo ngẫu nhiên)
        $maSo = $row['ma_so'] ?? 'SV' . rand(1000, 9999);

        // ✅ Ghi log để theo dõi (tuỳ chọn)
        Log::info("📥 Import/Update SV: {$maSo} - " . ($row['ho_ten'] ?? 'Không rõ tên'));

        // ✅ updateOrCreate sẽ:
        // - Update nếu SV đã tồn tại (cùng maSo)
        // - Tạo mới nếu chưa có
        SinhVien::updateOrCreate(
            ['maSo' => $maSo],
            [
                'hoTen'    => $row['ho_ten'] ?? null,
                'email'    => $row['email'] ?? null,
                'gioiTinh' => $row['gioi_tinh'] ?? null,
                'ngaySinh' => $row['ngay_sinh'] ?? null,
                'sdt'      => $row['sdt'] ?? null,
                'diaChi'   => $row['dia_chi'] ?? null,
                'maLop'    => $this->maLop,
                'matKhau'  => Hash::make('123456'), // mật khẩu mặc định
            ]
        );

        // ❗Trả về null để Laravel Excel không cố insert thêm (tránh lỗi trùng)
        return null;
    }
}
