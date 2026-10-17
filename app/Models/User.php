<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'so_dien_thoai',
        'dia_chi',
        'ngay_sinh',
        'password',
        'google2fa_secret',
        'ghi_chu_thu_thu',
        'khoa_muon_sach',
        'ly_do_khoa',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ngay_sinh' => 'date',
        'password' => 'hashed',
        'khoa_muon_sach' => 'boolean',
    ];

    public function phieuMuons(): HasMany
    {
        return $this->hasMany(PhieuMuon::class);
    }

    public function phieuPhats(): HasMany
    {
        return $this->hasMany(PhieuPhat::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function hasGoogle2Fa(): bool
    {
        return ! empty($this->google2fa_secret);
    }

    /**
     * Evaluate reader reliability / borrowing background (Credit Status).
     *
     * @return array{
     *     muc_do: string,
     *     ten_muc_do: string,
     *     nhan_hien_thi: string,
     *     badge_class: string,
     *     icon: string,
     *     mo_ta: string,
     *     co_the_muon: bool,
     *     ly_do_khong_the_muon: ?string,
     *     thong_ke: array
     * }
     */
    public function danhGiaLyLich(): array
    {
        $tongMuon = $this->phieuMuons()->count();
        $dangGiu = $this->phieuMuons()->whereIn('trang_thai', ['dang_muon', 'qua_han'])->count();

        $activeOverdueLoans = $this->phieuMuons()
            ->where('trang_thai', '!=', 'da_tra')
            ->whereDate('ngay_tra_du_kien', '<', today())
            ->get();
        $quaHan = $activeOverdueLoans->count();

        $maxSoNgayQuaHan = 0;
        foreach ($activeOverdueLoans as $loan) {
            $days = (int) $loan->ngay_tra_du_kien->diffInDays(today());
            if ($days > $maxSoNgayQuaHan) {
                $maxSoNgayQuaHan = $days;
            }
        }

        $phatChuaNop = $this->phieuPhats()->chuaNop()->count();
        $tienPhatNo = (int) $this->phieuPhats()->chuaNop()->sum('tong_tien_phat');
        $tongLanViPham = $this->phieuPhats()->count();

        $soLanTraSom = $this->phieuMuons()
            ->where('trang_thai', 'da_tra')
            ->whereNotNull('ngay_tra_thuc_te')
            ->whereNotNull('ngay_tra_du_kien')
            ->whereColumn('ngay_tra_thuc_te', '<', 'ngay_tra_du_kien')
            ->count();

        // 1. Level: Cấm mượn (Bị thủ thư khóa quyền HOẶC giữ sách quá hạn > 20 ngày)
        $isOver20DaysOverdue = $maxSoNgayQuaHan > PhieuPhat::MOC_NGAY_CAM_MUON;

        if ($this->khoa_muon_sach || $isOver20DaysOverdue) {
            $lyDo = $this->khoa_muon_sach
                ? ($this->ly_do_khoa ?: 'Tài khoản độc giả đang bị khóa quyền mượn sách do vi phạm nội quy thư viện.')
                : "Tài khoản bị đưa vào diện CẤM MƯỢN do đang giữ sách quá hạn {$maxSoNgayQuaHan} ngày (vượt quá 20 ngày quy định).";

            return [
                'muc_do' => 'cam_muon',
                'ten_muc_do' => 'Cấm mượn (Quá hạn > 20 ngày)',
                'nhan_hien_thi' => '⛔ Cấm mượn',
                'badge_class' => 'danger',
                'icon' => 'bi-slash-circle-fill',
                'mo_ta' => 'Tài khoản bị cấm mượn sách do vi phạm quá hạn trên 20 ngày hoặc bị khóa quyền.',
                'co_the_muon' => false,
                'ly_do_khong_the_muon' => $lyDo,
                'thong_ke' => compact('tongMuon', 'dangGiu', 'quaHan', 'phatChuaNop', 'tienPhatNo', 'tongLanViPham', 'maxSoNgayQuaHan', 'soLanTraSom'),
            ];
        }

        // 2. Level: Vi phạm (Mượn quá 5 ngày đến 20 ngày chưa trả HOẶC còn nợ tiền phạt)
        $isOverdueViolation = $maxSoNgayQuaHan >= 5 && $maxSoNgayQuaHan <= PhieuPhat::MOC_NGAY_CAM_MUON;

        if ($isOverdueViolation || $phatChuaNop > 0) {
            $lyDoParts = [];
            if ($isOverdueViolation) {
                $lyDoParts[] = "đang giữ sách quá hạn {$maxSoNgayQuaHan} ngày chưa hoàn trả (từ 5 - 20 ngày)";
            }
            if ($phatChuaNop > 0) {
                $tienFmt = number_format($tienPhatNo, 0, ',', '.').' VNĐ';
                $lyDoParts[] = "còn {$phatChuaNop} phiếu phạt chưa thanh toán ({$tienFmt})";
            }
            $lyDo = 'Độc giả không đủ điều kiện mượn sách do: '.implode(' và ', $lyDoParts).'. Cần hoàn tất trả sách và nộp phạt trước khi mượn tiếp.';

            return [
                'muc_do' => 'vi_pham',
                'ten_muc_do' => 'Vi phạm (Quá hạn ≥ 5 ngày / Nợ phạt)',
                'nhan_hien_thi' => '🔴 Vi phạm',
                'badge_class' => 'danger',
                'icon' => 'bi-exclamation-octagon-fill',
                'mo_ta' => 'Đang giữ sách quá hạn từ 5 - 20 ngày hoặc còn nợ tiền phạt.',
                'co_the_muon' => false,
                'ly_do_khong_the_muon' => $lyDo,
                'thong_ke' => compact('tongMuon', 'dangGiu', 'quaHan', 'phatChuaNop', 'tienPhatNo', 'tongLanViPham', 'maxSoNgayQuaHan', 'soLanTraSom'),
            ];
        }

        // 3. Level: Cảnh báo / Cần chú ý (Trễ hạn từ 1-4 ngày HOẶC từng bị phạt >= 2 lần HOẶC đang giữ >= 3 cuốn)
        $isOverdueWarning = $maxSoNgayQuaHan >= 1 && $maxSoNgayQuaHan <= 4;

        if ($isOverdueWarning || $tongLanViPham >= 2 || $dangGiu >= 3) {
            $moTa = $isOverdueWarning
                ? "Đang có sách trễ hạn nhẹ ({$maxSoNgayQuaHan} ngày), cần nhắc nhở hoàn trả."
                : 'Từng có tiền sử trễ hạn hoặc đang giữ nhiều sách.';

            return [
                'muc_do' => 'canh_bao',
                'ten_muc_do' => 'Cần chú ý (Trễ 1-4 ngày / Nhắc nhở)',
                'nhan_hien_thi' => '🟡 Cần chú ý',
                'badge_class' => 'warning',
                'icon' => 'bi-exclamation-triangle-fill',
                'mo_ta' => $moTa,
                'co_the_muon' => true,
                'ly_do_khong_the_muon' => null,
                'thong_ke' => compact('tongMuon', 'dangGiu', 'quaHan', 'phatChuaNop', 'tienPhatNo', 'tongLanViPham', 'maxSoNgayQuaHan', 'soLanTraSom'),
            ];
        }

        // 4. Level: Uy tín cao (Trả sớm cho 3 lần mượn trở lên, 0 lần phạt, không quá hạn)
        if ($soLanTraSom >= 3 && $tongLanViPham === 0 && $quaHan === 0 && $phatChuaNop === 0) {
            return [
                'muc_do' => 'uy_tin',
                'ten_muc_do' => 'Uy tín cao (Trả sớm ≥ 3 lần)',
                'nhan_hien_thi' => '🌟 Uy tín cao',
                'badge_class' => 'success',
                'icon' => 'bi-star-fill',
                'mo_ta' => "Độc giả gương mẫu với {$soLanTraSom} lần trả sách sớm hơn hạn quy định, không vi phạm.",
                'co_the_muon' => true,
                'ly_do_khong_the_muon' => null,
                'thong_ke' => compact('tongMuon', 'dangGiu', 'quaHan', 'phatChuaNop', 'tienPhatNo', 'tongLanViPham', 'maxSoNgayQuaHan', 'soLanTraSom'),
            ];
        }

        // 5. Level: Bình thường (Mượn trả đúng hạn hoặc độc giả mới)
        return [
            'muc_do' => 'binh_thuong',
            'ten_muc_do' => 'Bình thường (Đủ điều kiện)',
            'nhan_hien_thi' => '🟢 Bình thường',
            'badge_class' => 'info',
            'icon' => 'bi-shield-check',
            'mo_ta' => 'Lịch sử mượn trả đúng hạn hoặc độc giả mới, đủ điều kiện mượn sách.',
            'co_the_muon' => true,
            'ly_do_khong_the_muon' => null,
            'thong_ke' => compact('tongMuon', 'dangGiu', 'quaHan', 'phatChuaNop', 'tienPhatNo', 'tongLanViPham', 'maxSoNgayQuaHan', 'soLanTraSom'),
        ];
    }
}
