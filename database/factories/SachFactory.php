<?php

namespace Database\Factories;

use App\Models\Sach;
use App\Models\TheLoai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sach>
 */
class SachFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_sach' => fake()->sentence(4),
            'the_loai_id' => TheLoai::factory(),
            'nam_xuat_ban' => fake()->numberBetween(1990, 2023),
            'nha_xuat_ban' => fake()->randomElement(['NXB Trẻ', 'NXB Kim Đồng', 'NXB Văn Học']),
            'so_luong' => fake()->numberBetween(3, 10),
            'hinh_anh' => null,
            'mo_ta' => fake()->paragraph(),
        ];
    }
}
