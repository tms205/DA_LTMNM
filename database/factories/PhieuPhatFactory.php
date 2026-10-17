<?php

namespace Database\Factories;

use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhieuPhat>
 */
class PhieuPhatFactory extends Factory
{
    protected $model = PhieuPhat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $soNgayQuaHan = fake()->numberBetween(1, 10);
        $tinh = PhieuPhat::tinhTienPhat($soNgayQuaHan);

        return [
            'phieu_muon_id' => PhieuMuon::factory()->quaHan(),
            'user_id' => fn (array $attributes) => PhieuMuon::find($attributes['phieu_muon_id'])?->user_id ?? User::factory()->docGia(),
            'nguoi_thu_id' => null,
            'so_ngay_qua_han' => $soNgayQuaHan,
            'tien_phat_moi_ngay' => $tinh['don_gia'],
            'tong_tien_phat' => $tinh['tong_tien'],
            'ly_do' => "Quá hạn mượn sách ({$soNgayQuaHan} ngày)",
            'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
            'ngay_nop' => null,
            'ghi_chu' => null,
        ];
    }

    public function daNop(): static
    {
        return $this->state(fn (array $attributes): array => [
            'trang_thai' => PhieuPhat::TRANG_THAI_DA_NOP,
            'ngay_nop' => now(),
            'nguoi_thu_id' => User::factory()->admin(),
        ]);
    }
}
