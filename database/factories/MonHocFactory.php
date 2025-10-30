<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MonHocFactory extends Factory
{
    public function definition(): array
    {
        $monhoc = [
            'Cơ sở dữ liệu',
            'Cấu trúc dữ liệu và giải thuật',
            'Nhập môn lập trình',
            'Phân tích và thiết kế hệ thống',
            'Lập trình hướng đối tượng',
            'Trí tuệ nhân tạo',
            'Mạng máy tính',
            'Hệ điều hành',
        ];

        return [
            'maNganh' => 1,
            'maSoMon' => strtoupper($this->faker->lexify('SE1??')),
            'tenMon' => $this->faker->randomElement($monhoc),
            'soTinChi' => $this->faker->randomElement([2, 3, 4]),
            'moTa' => 'Môn học thuộc chuyên ngành CNTT',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
