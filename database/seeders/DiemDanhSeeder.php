<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiemDanhSeeder extends Seeder {
    public function run(): void {
        for ($i = 4; $i <= 63; $i++) {
            DB::table('diemdanhlog')->insert([
                'buoihoc_id' => 1,
                'sinhvien_id' => $i,
                'thoi_gian_diem_danh' => Carbon::now(),
                'trang_thai' => 'Co mat',
                'confidence' => rand(80, 99) + (rand(0,99)/100),
            ]);
        }
    }
}
