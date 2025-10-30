<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class SinhVienFactory extends Factory
{
    public function definition(): array
    {
        return [
            'maNganh' => 1, // hoặc bạn có thể random theo danh sách ngành
            'maLopHanhChinh' => 'D' . $this->faker->numberBetween(19, 24) . 'CNTT0' . $this->faker->numberBetween(1, 5),
            'maSo' => 'SV' . $this->faker->unique()->numerify('2020###'),
            'hoTen' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'matKhau' => Hash::make('sv123'),
            'soDienThoai' => '09' . $this->faker->numberBetween(10000000, 99999999),
            'khoaHoc' => $this->faker->numberBetween(2020, 2024),
            'anhDaiDien' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
