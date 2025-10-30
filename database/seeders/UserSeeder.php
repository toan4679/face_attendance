<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;               // nhớ đã có model User
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'email'    => 'admin@tlu.edu.vn',
            'password' => Hash::make('123456'),
            'ho_ten'   => 'Quản trị viên',
            'role'     => 'ADMIN',
        ]);

        // Giảng viên
        User::create([
            'email'    => 'nguyenvana@tlu.edu.vn',
            'password' => Hash::make('123456'),
            'ho_ten'   => 'Nguyễn Văn A',
            'role'     => 'GV',
            'bomon_id' => 1,
        ]);

        User::create([
            'email'    => 'tranthib@tlu.edu.vn',
            'password' => Hash::make('123456'),
            'ho_ten'   => 'Trần Thị B',
            'role'     => 'GV',
            'bomon_id' => 2,
        ]);

        // Sinh viên
        for ($i = 1; $i <= 60; $i++) {
            User::create([
                'email'            => "sv{$i}@tlu.edu.vn",
                'password'         => Hash::make('123456'),
                'ho_ten'           => "Sinh viên {$i}",
                'role'             => 'SV',
                'lophanhchinh_id'  => random_int(1, 4),
                'ma_sv'            => 'SV64' . str_pad((string)$i, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
