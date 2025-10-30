<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiaiDoanHocSeeder extends Seeder {
    public function run(): void {
        DB::table('giaidoanhoc')->insert([
            ['ten_giai_doan' => 'Học kỳ I năm 2025-2026', 'ngay_bat_dau' => '2025-09-01', 'ngay_ket_thuc' => '2026-01-15'],
            ['ten_giai_doan' => 'Học kỳ II năm 2025-2026', 'ngay_bat_dau' => '2026-02-15', 'ngay_ket_thuc' => '2026-06-01'],
        ]);
    }
}
