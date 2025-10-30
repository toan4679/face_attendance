<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('monhoc')->insert([
            'maNganh' => 1,
            'maSoMon' => 'SE101',
            'tenMon' => 'Nhập môn Công nghệ phần mềm',
            'soTinChi' => 3,
            'moTa' => 'Môn học cơ sở ngành',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
