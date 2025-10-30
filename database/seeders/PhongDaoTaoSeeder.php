<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PhongDaoTaoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('phongdaotao')->insert([
            'maAdmin' => 1,
            'hoTen' => 'Nguyễn Thị Hạnh',
            'email' => 'pdt@university.edu.vn',
            'matKhau' => Hash::make('pdt123'),
            'soDienThoai' => '0912345678',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
