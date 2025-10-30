<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->insert([
            'hoTen' => 'Quản trị hệ thống',
            'email' => 'admin@university.edu.vn',
            'matKhau' => Hash::make('admin123'),
            'soDienThoai' => '0909123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
