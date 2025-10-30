<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder {
    public function run(): void {
        DB::table('khoa')->insert([
            ['ten_khoa' => 'Công nghệ thông tin'],
            ['ten_khoa' => 'Cơ khí'],
            ['ten_khoa' => 'Kinh tế'],
            ['ten_khoa' => 'Điện – Điện tử'],
        ]);
    }
}
