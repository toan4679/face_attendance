<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            PhongDaoTaoSeeder::class,
            KhoaBoMonSeeder::class,
            GiangVienSeeder::class,
            SinhVienSeeder::class,
            MonHocSeeder::class,
            LopHocPhanSeeder::class,
            BuoiHocSeeder::class,
            DangKyHocSeeder::class,
            DiemDanhSeeder::class,
            KhuonMatSeeder::class,
            ThongBaoSeeder::class,
        ]);

        // Gọi Factory để sinh dữ liệu ngẫu nhiên
        \App\Models\GiangVien::factory(5)->create();
        \App\Models\SinhVien::factory(50)->create();
        \App\Models\MonHoc::factory(10)->create();
    }
}
