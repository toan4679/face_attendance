<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LopHocPhanSeeder extends Seeder {
    public function run(): void {
        DB::table('lophocphan')->insert([
            ['ten_LHP' => 'Lập trình Java - Nhóm 1', 'hocphan_id' => 1, 'giaidoan_id' => 1, 'giangvien_chinh_id' => 2, 'loai' => 'LT', 'max_sv' => 60],
            ['ten_LHP' => 'Cơ sở dữ liệu - Nhóm 2', 'hocphan_id' => 2, 'giaidoan_id' => 1, 'giangvien_chinh_id' => 3, 'loai' => 'LT', 'max_sv' => 60],
        ]);
    }
}
