<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhongHocSeeder extends Seeder {
    public function run(): void {
        DB::table('phonghoc')->insert([
            ['ten_phong' => 'D5-201', 'suc_chua' => 70, 'latitude' => 21.00712, 'longitude' => 105.82345],
            ['ten_phong' => 'D5-202', 'suc_chua' => 60, 'latitude' => 21.00710, 'longitude' => 105.82342],
            ['ten_phong' => 'D5-203', 'suc_chua' => 80, 'latitude' => 21.00711, 'longitude' => 105.82347],
        ]);
    }
}
