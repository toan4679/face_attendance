<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LopHanhChinhSeeder extends Seeder {
    public function run(): void {
        DB::table('lophanhchinh')->insert([
            ['ten_lop' => '64CNTT1', 'nganh_id' => 1],
            ['ten_lop' => '64CNTT2', 'nganh_id' => 1],
            ['ten_lop' => '64HTTT',  'nganh_id' => 3],
            ['ten_lop' => '64AI1',   'nganh_id' => 2],
        ]);
    }
}
