<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NganhDaoTaoSeeder extends Seeder {
    public function run(): void {
        DB::table('nganhdaotao')->insert([
            ['ten_nganh' => 'Công nghệ phần mềm', 'bomon_id' => 1],
            ['ten_nganh' => 'Trí tuệ nhân tạo', 'bomon_id' => 1],
            ['ten_nganh' => 'Hệ thống thông tin quản lý', 'bomon_id' => 2],
            ['ten_nganh' => 'Cơ điện tử', 'bomon_id' => 3],
            ['ten_nganh' => 'Kế toán doanh nghiệp', 'bomon_id' => 4],
        ]);
    }
}
