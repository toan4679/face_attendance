<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BuoiHocKeHoachSeeder extends Seeder {
    public function run(): void {
        $ngay = Carbon::create(2025, 9, 3);
        for ($i = 1; $i <= 10; $i++) {
            DB::table('buoihockehoach')->insert([
                'lophocphan_id' => 1,
                'phonghoc_id' => 1,
                'thoi_gian_bat_dau' => $ngay->copy()->addDays($i*7),
                'so_tiet' => 2,
                'trang_thai' => 'Chua dien ra',
                'giangvien_day_id' => 2,
            ]);
        }
    }
}

