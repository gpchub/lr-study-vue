<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HocVien>
 */
class HocVienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gioi_tinh = fake()->randomElement(['1', '2']);
        $gender = $gioi_tinh == 1 ? 'male' : 'female';
        return [
            'ho' => fake()->lastName($gender) . ' ' . fake()->middleName($gender),
            'ten' => fake()->firstName($gender),
            'email' => fake()->unique()->safeEmail(),
            'dien_thoai' => fake()->phoneNumber(),
            'ngay_sinh' => fake()->dateTimeBetween('-30 years', '-15 years'),
            'gioi_tinh' => $gioi_tinh,
        ];
    }
}
