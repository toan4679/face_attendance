<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaceSeeder extends Seeder
{
    public function run(): void
    {
        // Mỗi sinh viên (role = 'SV') sẽ có 1 embedding khuôn mặt
        $sinhviens = DB::table('users')->where('role', 'SV')->get();

        foreach ($sinhviens as $sv) {
            // Tạo một embedding ngẫu nhiên 128 chiều (face-api.js thường dùng 128-d vector)
            $embedding = [];
            for ($i = 0; $i < 128; $i++) {
                $embedding[] = round(mt_rand(-1000, 1000) / 1000, 6); // giá trị float từ -1 đến 1
            }

            DB::table('faces')->insert([
                'user_id' => $sv->user_id,
                'embedding' => json_encode($embedding),
                'image_url' => "https://randomuser.me/api/portraits/men/" . rand(1, 99) . ".jpg",
                'captured_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Thêm vài giảng viên cũng có embedding để test
        $giangviens = DB::table('users')->where('role', 'GV')->get();

        foreach ($giangviens as $gv) {
            $embedding = [];
            for ($i = 0; $i < 128; $i++) {
                $embedding[] = round(mt_rand(-1000, 1000) / 1000, 6);
            }

            DB::table('faces')->insert([
                'user_id' => $gv->user_id,
                'embedding' => json_encode($embedding),
                'image_url' => "https://randomuser.me/api/portraits/women/" . rand(1, 99) . ".jpg",
                'captured_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
