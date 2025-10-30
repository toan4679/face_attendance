<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongBaoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('thongbao')->insert([
            'tieuDe' => 'Lịch học tuần mới',
            'noiDung' => 'Buổi học SE101-L01 sẽ diễn ra lúc 7h30 sáng thứ 2 tại phòng A201.',
            'nguoiGui' => 'Phòng Đào Tạo',
            'nguoiNhanLoai' => 'SinhVien',
            'maNguoiNhan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
