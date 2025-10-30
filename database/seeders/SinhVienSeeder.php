<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SinhVienSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sinhvien')->insert([
            'maNganh' => 1,
            'maLopHanhChinh' => 'D20CNTT01',
            'maSo' => 'SV2020001',
            'hoTen' => 'Nguyễn Văn Huy',
            'email' => 'huy.nguyen@university.edu.vn',
            'matKhau' => Hash::make('sv123'),
            'soDienThoai' => '0977123456',
            'khoaHoc' => 2020,
            'anhDaiDien' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
