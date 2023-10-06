<?php

namespace Database\Factories;

use App\Models\ChiNhanh;
use App\Models\ChungChi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LichThi>
 */
class LichThiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listChungChi = ChungChi::pluck('id');
        $listChiNhanh = ChiNhanh::pluck('ten');

        $ngay_thi = fake()->dateTimeBetween('-1 month', '1 month');
        $ngay_thi->setTime(fake()->numberBetween(8, 15), fake()->randomElement([0, 30]));

        return [
            'chung_chi_id' => fake()->randomElement($listChungChi),
            'ngay_thi' => $ngay_thi,
            'dia_diem' => 'Chi nhánh ' . fake()->randomElement($listChiNhanh),
        ];
    }
}
