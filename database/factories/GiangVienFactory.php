<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GiangVienFactory extends Factory
{
    public function definition(): array
    {
        $hocVi = $this->faker->randomElement(['Thạc sĩ', 'Tiến sĩ', 'Phó giáo sư']);

        return [
            'maBoMon' => 1,
            'hoTen' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'matKhau' => Hash::make('gv123'),
            'soDienThoai' => '09' . $this->faker->numberBetween(10000000, 99999999),
            'hocVi' => $hocVi,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
