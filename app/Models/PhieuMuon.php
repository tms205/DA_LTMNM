<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'phieu_muons';

    protected $fillable = [
        'user_id',
        'sach_id',
        'ngay_muon',
        'ngay_tra_du_kien',
        'ngay_tra_thuc_te',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_muon' => 'date',
        'ngay_tra_du_kien' => 'date',
        'ngay_tra_thuc_te' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sach(): BelongsTo
    {
        return $this->belongsTo(Sach::class);
    }

    public function phieuPhat(): HasOne
    {
        return $this->hasOne(PhieuPhat::class);
    }

    public function scopeCuaNguoiDung(Builder $query, User $user): Builder
    {
        return $query->whereBelongsTo($user);
    }

    public function isQuaHan(): bool
    {
        return $this->trang_thai !== 'da_tra'
            && $this->ngay_tra_du_kien->isBefore(today());
    }

    public function trangThaiHienThi(): string
    {
        return $this->isQuaHan() ? 'qua_han' : $this->trang_thai;
    }

    /**
     * Check if the loan has an actual return date that exceeds the expected return date,
     * and create or update the penalty fine ticket.
     * Note: Fine ticket is ONLY created when actual return date is strictly after expected return date.
     */
    public function kiemTraVaTaoPhieuPhat(?Carbon $ngayTra = null): ?PhieuPhat
    {
        $ngayTraThucTe = $ngayTra ?? ($this->ngay_tra_thuc_te ? Carbon::parse($this->ngay_tra_thuc_te) : null);

        // Phiếu phạt CHỈ ĐƯỢC SINH RA khi ngày trả thực tế lớn hơn ngày trả dự kiến
        if (! $ngayTraThucTe || ! $this->ngay_tra_du_kien || ! $ngayTraThucTe->isAfter($this->ngay_tra_du_kien)) {
            return null;
        }

        $soNgayQuaHan = (int) $this->ngay_tra_du_kien->diffInDays($ngayTraThucTe);
        if ($soNgayQuaHan < 1) {
            $soNgayQuaHan = 1;
        }

        $tinhPhat = PhieuPhat::tinhTienPhat($soNgayQuaHan);
        $donGia = $tinhPhat['don_gia'];
        $tongTien = $tinhPhat['tong_tien'];
        $tenSach = $this->sach?->ten_sach ?? 'Sách #'.$this->sach_id;
        $lyDo = "Quá hạn mượn sách \"{$tenSach}\" ({$soNgayQuaHan} ngày. {$tinhPhat['dien_giai']})";

        if ($this->phieuPhat) {
            if ($this->phieuPhat->isChuaNop()) {
                $this->phieuPhat->update([
                    'so_ngay_qua_han' => $soNgayQuaHan,
                    'tien_phat_moi_ngay' => $donGia,
                    'tong_tien_phat' => $tongTien,
                    'ly_do' => $lyDo,
                ]);
            }

            return $this->phieuPhat;
        }

        return $this->phieuPhat()->create([
            'user_id' => $this->user_id,
            'so_ngay_qua_han' => $soNgayQuaHan,
            'tien_phat_moi_ngay' => $donGia,
            'tong_tien_phat' => $tongTien,
            'ly_do' => $lyDo,
            'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
        ]);
    }
}
