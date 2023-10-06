<?php

namespace Database\Factories;

use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HocPhi>
 */
class HocPhiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hoc_vien = HocVien::inRandomOrder()->first();
        $lop_hoc = LopHoc::inRandomOrder()->first();
        $so_tien = 1200000;
        $thang = fake()->numberBetween(1, 12);
        $nam = fake()->numberBetween(2022, 2023);
        $ngay_dong = Carbon::createFromDate($nam, $thang, fake()->numberBetween(1, 28));

        return [
            'hoc_vien_id' => $hoc_vien->id,
            'lop_hoc_id' => $lop_hoc->id,
            'so_tien' => $so_tien,
            'thang' => $thang,
            'nam' => $nam,
            'ngay_dong' => $ngay_dong
        ];
    }
}
