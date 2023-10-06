<?php

namespace Database\Factories;

use App\Models\HocVien;
use App\Models\LichThi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LichThiHocVien>
 */
class LichThiHocVienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hocVien = HocVien::inRandomOrder()->first();
        $lichThi = LichThi::inRandomOrder()->first();

        return [
            'hoc_vien_id' => $hocVien->id,
            'lich_thi_id' => $lichThi->id,
            'tinh_trang' => 0,
            'ket_qua' => 0
        ];
    }
}
