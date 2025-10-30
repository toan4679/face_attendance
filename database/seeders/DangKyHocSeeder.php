<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DangKyHocSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dangkyhoc')->insert([
            'maLopHP' => 1,
            'maSV' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
