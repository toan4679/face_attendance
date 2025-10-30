<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LopHocPhanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lophocphan')->insert([
            'maMon' => 1,
            'maGV' => 1,
            'maSoLopHP' => 'SE101-L01',
            'hocKy' => 'HK1',
            'namHoc' => '2024-2025',
            'thongTinLichHoc' => 'Thứ 2 tiết 1-3, phòng A201',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
