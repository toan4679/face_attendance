<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhuonMatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('khuonmat')->insert([
            'maSV' => 1,
            'duongDanAnh' => 'storage/faces/sv2020001.jpg',
            'duLieuNhanDien' => json_encode(['vector' => [0.12, 0.45, -0.33, 0.27, -0.14, 0.89, -0.56]]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
