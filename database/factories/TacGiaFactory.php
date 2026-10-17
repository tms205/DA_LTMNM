<?php

namespace Database\Factories;

use App\Models\TacGia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TacGia>
 */
class TacGiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_tac_gia' => fake()->name(),
            'nam_sinh' => fake()->numberBetween(1940, 2000),
            'quoc_tich' => 'Việt Nam',
            'tieu_su' => fake()->sentence(),
        ];
    }
}
