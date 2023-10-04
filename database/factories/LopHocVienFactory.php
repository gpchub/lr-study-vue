<?php

namespace Database\Factories;

use App\Models\HocVien;
use App\Models\LopHoc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LopHocVien>
 */
class LopHocVienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hoc_vien = HocVien::inRandomOrder()->first();
        $lop = LopHoc::inRandomOrder()->first();
        $ngay_bat_dau = fake()->dateTimeBetween('-1 years');

        return [
            'hoc_vien_id' => $hoc_vien->id,
            'lop_hoc_id' => $lop->id,
            'tinh_trang' => 1,
            'ngay_bat_dau' => $ngay_bat_dau
        ];
    }
}
