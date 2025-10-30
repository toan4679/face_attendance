<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KhoaSeeder::class,
            BoMonSeeder::class,
            NganhDaoTaoSeeder::class,
            LopHanhChinhSeeder::class,
            HocPhanSeeder::class,
            GiaiDoanHocSeeder::class,
            PhongHocSeeder::class,
            UserSeeder::class,
            FaceSeeder::class,     
            LopHocPhanSeeder::class,
            EnrollmentSeeder::class,
            BuoiHocKeHoachSeeder::class,
            DiemDanhSeeder::class,
        ]);
    }
}
