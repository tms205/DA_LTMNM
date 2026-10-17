<?php

namespace Database\Factories;

use App\Models\TheLoai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TheLoai>
 */
class TheLoaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_the_loai' => fake()->unique()->words(2, true),
            'mo_ta' => fake()->sentence(),
        ];
    }
}
