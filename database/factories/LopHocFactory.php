<?php

namespace Database\Factories;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LopHoc>
 */
class LopHocFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake();
        $hinh_thuc = $faker->randomElement(['Studio', 'Online', 'PT']);
        $chi_nhanh = ChiNhanh::inRandomOrder()->first();
        $number = $faker->numberBetween(1, 9);
        $giao_vien = GiaoVien::inRandomOrder()->first();
        $ca_hoc = $faker->randomElement([
            '246 - 05:45',
            '246 - 08:00',
            '246 - 17:30',
            '246 - 18:15',
            '246 - 18:45',
            '246 - 20:00',
            '357 - 06:00',
            '357 - 08:00',
            '357 - 17:00',
            '357 - 18:15',
            '357 - 18:30',
            '357 - 19:30',
        ]);

        return [
            'ten' => "{$hinh_thuc} {$chi_nhanh->ten} {$number}",
            'ca_hoc' => $ca_hoc,
            'chi_nhanh_id' => $chi_nhanh->id,
            'giao_vien_id' => $giao_vien->id,
        ];
    }
}
