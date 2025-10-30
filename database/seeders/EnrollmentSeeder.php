<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder {
    public function run(): void {
        for ($i = 4; $i <= 63; $i++) { // sinh viên bắt đầu từ id 4
            DB::table('enrollments')->insert([
                ['lophocphan_id' => 1, 'sinhvien_id' => $i],
                ['lophocphan_id' => 2, 'sinhvien_id' => $i],
            ]);
        }
    }
}
