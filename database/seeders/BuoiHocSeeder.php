<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuoiHocSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('buoihoc')->insert([
            'maLopHP' => 1,
            'maGV' => 1,
            'ngayHoc' => now()->toDateString(),
            'gioBatDau' => '07:30:00',
            'gioKetThuc' => '09:30:00',
            'phongHoc' => 'A201',
            'maQR' => 'QR123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
