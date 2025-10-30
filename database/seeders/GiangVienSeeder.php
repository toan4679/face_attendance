<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GiangVienSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('giangvien')->insert([
            'maBoMon' => 1,
            'hoTen' => 'ThS. Trần Văn Minh',
            'email' => 'minh.tran@university.edu.vn',
            'matKhau' => Hash::make('gv123'),
            'soDienThoai' => '0987654321',
            'hocVi' => 'Thạc sĩ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
