<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaBoMonSeeder extends Seeder
{
    public function run(): void
    {
        // Khoa
        DB::table('khoa')->insert([
            'tenKhoa' => 'Công nghệ thông tin',
            'moTa' => 'Đào tạo kỹ sư, cử nhân CNTT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Bộ môn
        DB::table('bomon')->insert([
            'maKhoa' => 1,
            'tenBoMon' => 'Kỹ thuật phần mềm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ngành
        DB::table('nganh')->insert([
            'maBoMon' => 1,
            'tenNganh' => 'Công nghệ phần mềm',
            'maSo' => '7480103',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
