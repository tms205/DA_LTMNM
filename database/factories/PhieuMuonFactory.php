<?php

namespace Database\Factories;

use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhieuMuon>
 */
class PhieuMuonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->docGia(),
            'sach_id' => Sach::factory(),
            'ngay_muon' => today()->subDays(3),
            'ngay_tra_du_kien' => today()->addDays(7),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
            'ghi_chu' => null,
        ];
    }

    public function daTra(): static
    {
        return $this->state(fn (array $attributes): array => [
            'ngay_tra_thuc_te' => today(),
            'trang_thai' => 'da_tra',
        ]);
    }

    public function quaHan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'ngay_muon' => today()->subDays(14),
            'ngay_tra_du_kien' => today()->subDay(),
            'trang_thai' => 'qua_han',
        ]);
    }
}
