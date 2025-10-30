<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiemDanhSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('diemdanh')->insert([
            'maBuoi' => 1,
            'maSV' => 1,
            'trangThai' => 'Có mặt',
            'thoiGianDiemDanh' => now(),
            'hinhThuc' => 'Khuôn mặt',
            'xacThucKhuonMat' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
