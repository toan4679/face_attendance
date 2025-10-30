<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoMonSeeder extends Seeder {
    public function run(): void {
        DB::table('bomon')->insert([
            ['ten_bo_mon' => 'Khoa học máy tính', 'khoa_id' => 1],
            ['ten_bo_mon' => 'Hệ thống thông tin', 'khoa_id' => 1],
            ['ten_bo_mon' => 'Cơ điện tử', 'khoa_id' => 2],
            ['ten_bo_mon' => 'Kế toán', 'khoa_id' => 3],
            ['ten_bo_mon' => 'Tự động hóa', 'khoa_id' => 4],
        ]);
    }
}

