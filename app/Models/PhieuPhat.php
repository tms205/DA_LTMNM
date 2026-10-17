<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuPhat extends Model
{
    use HasFactory;

    public const TIEN_PHAT_CO_BAN = 3000;

    public const TIEN_PHAT_TANG_CUONG = 5000;

    public const MOC_NGAY_TANG_CUONG = 10;

    public const MOC_NGAY_CAM_MUON = 20;

    public const TIEN_PHAT_MOI_NGAY = 3000;

    public const TRANG_THAI_CHUA_NOP = 'chua_nop';

    public const TRANG_THAI_DA_NOP = 'da_nop';

    /**
     * Calculate fine amount based on overdue days.
     *
     * @return array{don_gia: int, tong_tien: int, dien_giai: string, qua_20_ngay: bool}
     */
    public static function tinhTienPhat(int $soNgayQuaHan): array
    {
        if ($soNgayQuaHan <= 0) {
            return [
                'don_gia' => self::TIEN_PHAT_CO_BAN,
                'tong_tien' => 0,
                'dien_giai' => '0đ',
                'qua_20_ngay' => false,
            ];
        }

        $qua20Ngay = $soNgayQuaHan > self::MOC_NGAY_CAM_MUON;

        if ($soNgayQuaHan <= self::MOC_NGAY_TANG_CUONG) {
            $tongTien = $soNgayQuaHan * self::TIEN_PHAT_CO_BAN;
            $donGia = self::TIEN_PHAT_CO_BAN;
            $dienGiai = "{$soNgayQuaHan} ngày × 3.000đ = ".number_format($tongTien, 0, ',', '.').'đ';
        } else {
            $ngayVuot = $soNgayQuaHan - self::MOC_NGAY_TANG_CUONG;
            $tongTien = (self::MOC_NGAY_TANG_CUONG * self::TIEN_PHAT_CO_BAN) + ($ngayVuot * self::TIEN_PHAT_TANG_CUONG);
            $donGia = self::TIEN_PHAT_TANG_CUONG;
            $dienGiai = "10 ngày đầu (30.000đ) + {$ngayVuot} ngày sau × 5.000đ = ".number_format($tongTien, 0, ',', '.').'đ';
        }

        return [
            'don_gia' => $donGia,
            'tong_tien' => $tongTien,
            'dien_giai' => $dienGiai,
            'qua_20_ngay' => $qua20Ngay,
        ];
    }

    protected $table = 'phieu_phats';

    protected $fillable = [
        'phieu_muon_id',
        'user_id',
        'nguoi_thu_id',
        'so_ngay_qua_han',
        'tien_phat_moi_ngay',
        'tong_tien_phat',
        'ly_do',
        'trang_thai',
        'ngay_nop',
        'ghi_chu',
    ];

    protected $casts = [
        'so_ngay_qua_han' => 'integer',
        'tien_phat_moi_ngay' => 'integer',
        'tong_tien_phat' => 'integer',
        'ngay_nop' => 'datetime',
    ];

    public function phieuMuon(): BelongsTo
    {
        return $this->belongsTo(PhieuMuon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nguoiThu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_thu_id');
    }

    public function isDaNop(): bool
    {
        return $this->trang_thai === self::TRANG_THAI_DA_NOP;
    }

    public function isChuaNop(): bool
    {
        return $this->trang_thai === self::TRANG_THAI_CHUA_NOP;
    }

    public function scopeChuaNop(Builder $query): Builder
    {
        return $query->where('trang_thai', self::TRANG_THAI_CHUA_NOP);
    }

    public function scopeDaNop(Builder $query): Builder
    {
        return $query->where('trang_thai', self::TRANG_THAI_DA_NOP);
    }

    public function scopeCuaNguoiDung(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Mark penalty as paid.
     */
    public function xacNhanNop(?User $nguoiThu = null, ?string $ghiChu = null): void
    {
        $this->update([
            'trang_thai' => self::TRANG_THAI_DA_NOP,
            'ngay_nop' => Carbon::now(),
            'nguoi_thu_id' => $nguoiThu?->id,
            'ghi_chu' => $ghiChu ?? $this->ghi_chu,
        ]);
    }
}
