<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HocPhanSeeder extends Seeder {
    public function run(): void {
        DB::table('hocphan')->insert([
            ['ten_hoc_phan' => 'Lập trình Java', 'so_tin_chi' => 3, 'so_tiet_LT' => 30, 'so_tiet_TH' => 15, 'bomon_id' => 1],
            ['ten_hoc_phan' => 'Cơ sở dữ liệu', 'so_tin_chi' => 3, 'so_tiet_LT' => 30, 'so_tiet_TH' => 15, 'bomon_id' => 2],
            ['ten_hoc_phan' => 'Trí tuệ nhân tạo', 'so_tin_chi' => 3, 'so_tiet_LT' => 30, 'so_tiet_TH' => 15, 'bomon_id' => 1],
        ]);
    }
}
