<?php

namespace Database\Seeders;

use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\Sach;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class PhieuMuonSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->first();
        if (! $admin) {
            throw new RuntimeException('Hãy chạy UserSeeder trước PhieuMuonSeeder.');
        }

        $saches = Sach::query()->get()->keyBy('ten_sach');
        if ($saches->isEmpty()) {
            throw new RuntimeException('Hãy chạy SachSeeder trước PhieuMuonSeeder.');
        }

        $users = User::query()->where('role', 'user')->get()->keyBy('email');

        $now = Carbon::today();

        // 1. Lê Hoàng Uy Tín (uytin.lehoang@gmail.com) -> 🌟 Uy tín cao (Trả sớm 5 lần)
        if ($u = $users->get('uytin.lehoang@gmail.com')) {
            $earlyLoans = [
                ['sach' => 'Truyện Kiều', 'sub_borrow' => 60, 'sub_due' => 46, 'sub_return' => 50], // sớm 4 ngày
                ['sach' => 'Chí Phèo', 'sub_borrow' => 45, 'sub_due' => 31, 'sub_return' => 35],   // sớm 4 ngày
                ['sach' => 'Dế Mèn phiêu lưu ký', 'sub_borrow' => 30, 'sub_due' => 16, 'sub_return' => 20], // sớm 4 ngày
                ['sach' => 'Mắt biếc', 'sub_borrow' => 25, 'sub_due' => 11, 'sub_return' => 15],   // sớm 4 ngày
                ['sach' => 'Đắc Nhân Tâm', 'sub_borrow' => 15, 'sub_due' => 1, 'sub_return' => 5],  // sớm 4 ngày
            ];

            foreach ($earlyLoans as $l) {
                if ($s = $saches->get($l['sach'])) {
                    PhieuMuon::query()->create([
                        'user_id' => $u->id,
                        'sach_id' => $s->id,
                        'ngay_muon' => $now->copy()->subDays($l['sub_borrow'])->toDateString(),
                        'ngay_tra_du_kien' => $now->copy()->subDays($l['sub_due'])->toDateString(),
                        'ngay_tra_thuc_te' => $now->copy()->subDays($l['sub_return'])->toDateString(),
                        'trang_thai' => 'da_tra',
                        'ghi_chu' => 'Độc giả đọc xong trả sớm hơn dự kiến, sách còn rất mới.',
                    ]);
                }
            }
        }

        // 2. Trần Thị Minh Châu (uytin.tranminh@gmail.com) -> 🌟 Uy tín cao (Trả sớm 3 lần, đúng hạn 1 lần)
        if ($u = $users->get('uytin.tranminh@gmail.com')) {
            $loans = [
                ['sach' => 'Hoàng tử bé', 'sub_borrow' => 40, 'sub_due' => 26, 'sub_return' => 30], // sớm 4 ngày
                ['sach' => 'Sapiens: Lược sử loài người', 'sub_borrow' => 30, 'sub_due' => 16, 'sub_return' => 19], // sớm 3 ngày
                ['sach' => 'Rừng Na Uy', 'sub_borrow' => 20, 'sub_due' => 6, 'sub_return' => 8],    // sớm 2 ngày
                ['sach' => 'Tắt Đèn', 'sub_borrow' => 18, 'sub_due' => 4, 'sub_return' => 4],       // đúng hạn
            ];

            foreach ($loans as $l) {
                if ($s = $saches->get($l['sach'])) {
                    PhieuMuon::query()->create([
                        'user_id' => $u->id,
                        'sach_id' => $s->id,
                        'ngay_muon' => $now->copy()->subDays($l['sub_borrow'])->toDateString(),
                        'ngay_tra_du_kien' => $now->copy()->subDays($l['sub_due'])->toDateString(),
                        'ngay_tra_thuc_te' => $now->copy()->subDays($l['sub_return'])->toDateString(),
                        'trang_thai' => 'da_tra',
                        'ghi_chu' => 'Trả sách đúng quy định.',
                    ]);
                }
            }
        }

        // 3. Nguyễn Văn An (binhthuong.nguyenvan@gmail.com) -> 🟢 Bình thường (Trả đúng hạn 3 lần)
        if ($u = $users->get('binhthuong.nguyenvan@gmail.com')) {
            $onTimeLoans = [
                ['sach' => 'Lão Hạc', 'sub_borrow' => 45, 'sub_due' => 31, 'sub_return' => 31],
                ['sach' => 'Vợ chồng A Phủ', 'sub_borrow' => 30, 'sub_due' => 16, 'sub_return' => 16],
                ['sach' => 'Số Đỏ', 'sub_borrow' => 15, 'sub_due' => 1, 'sub_return' => 1],
            ];

            foreach ($onTimeLoans as $l) {
                if ($s = $saches->get($l['sach'])) {
                    PhieuMuon::query()->create([
                        'user_id' => $u->id,
                        'sach_id' => $s->id,
                        'ngay_muon' => $now->copy()->subDays($l['sub_borrow'])->toDateString(),
                        'ngay_tra_du_kien' => $now->copy()->subDays($l['sub_due'])->toDateString(),
                        'ngay_tra_thuc_te' => $now->copy()->subDays($l['sub_return'])->toDateString(),
                        'trang_thai' => 'da_tra',
                        'ghi_chu' => 'Trả sách đúng ngày hẹn.',
                    ]);
                }
            }
        }

        // 4. Phạm Thị Mai (binhthuong.phamthi@gmail.com) -> 🟢 Bình thường (Đang mượn 1 cuốn trong hạn)
        if ($u = $users->get('binhthuong.phamthi@gmail.com')) {
            if ($s1 = $saches->get('Quẳng gánh lo đi và vui sống')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s1->id,
                    'ngay_muon' => $now->copy()->subDays(5)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->addDays(9)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'dang_muon',
                    'ghi_chu' => 'Đang mượn sách về nhà, hạn trả còn 9 ngày.',
                ]);
            }
            if ($s2 = $saches->get('Cho tôi xin một vé đi tuổi thơ')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s2->id,
                    'ngay_muon' => $now->copy()->subDays(30)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(16)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(16)->toDateString(),
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Đã hoàn trả đúng hạn.',
                ]);
            }
        }

        // 5. Hoàng Đức Nam (canhbao.trenhe@gmail.com) -> 🟡 Cần chú ý (Trễ hạn 2 ngày)
        if ($u = $users->get('canhbao.trenhe@gmail.com')) {
            if ($s = $saches->get('Sống mòn')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(16)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(2)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'qua_han',
                    'ghi_chu' => 'Quá hạn 2 ngày. Thủ thư đã liên hệ nhắc nhở.',
                ]);
            }
        }

        // 6. Đỗ Thị Hồng (canhbao.nhieusach@gmail.com) -> 🟡 Cần chú ý (Đang giữ cùng lúc 3 cuốn sách)
        if ($u = $users->get('canhbao.nhieusach@gmail.com')) {
            $multiBooks = ['Clean Code: A Handbook of Agile Software Craftsmanship', 'Clean Architecture: A Craftsman\'s Guide', 'Homo Deus: Lược sử tương lai'];
            foreach ($multiBooks as $bookName) {
                if ($s = $saches->get($bookName)) {
                    PhieuMuon::query()->create([
                        'user_id' => $u->id,
                        'sach_id' => $s->id,
                        'ngay_muon' => $now->copy()->subDays(3)->toDateString(),
                        'ngay_tra_du_kien' => $now->copy()->addDays(11)->toDateString(),
                        'ngay_tra_thuc_te' => null,
                        'trang_thai' => 'dang_muon',
                        'ghi_chu' => 'Mượn tài liệu làm khóa luận tốt nghiệp.',
                    ]);
                }
            }
        }

        // 7. Vũ Hải Đăng (canhbao.tiensu@gmail.com) -> 🟡 Cần chú ý (Từng bị phạt 2 lần - đã nộp đủ)
        if ($u = $users->get('canhbao.tiensu@gmail.com')) {
            if ($s1 = $saches->get('Giông Tố')) {
                $pm1 = PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s1->id,
                    'ngay_muon' => $now->copy()->subDays(40)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(26)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(23)->toDateString(), // trễ 3 ngày
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Trả trễ 3 ngày do bận công tác.',
                ]);
                PhieuPhat::query()->create([
                    'phieu_muon_id' => $pm1->id,
                    'user_id' => $u->id,
                    'nguoi_thu_id' => $admin->id,
                    'so_ngay_qua_han' => 3,
                    'tien_phat_moi_ngay' => 3000,
                    'tong_tien_phat' => 9000,
                    'ly_do' => 'Quá hạn mượn sách "Giông Tố" (3 ngày. 3 ngày × 3.000đ = 9.000đ)',
                    'trang_thai' => PhieuPhat::TRANG_THAI_DA_NOP,
                    'ngay_nop' => $now->copy()->subDays(23)->toDateTimeString(),
                    'ghi_chu' => 'Thu tiền mặt tại quầy thủ thư.',
                ]);
            }

            if ($s2 = $saches->get('Kafka bên bờ biển')) {
                $pm2 = PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s2->id,
                    'ngay_muon' => $now->copy()->subDays(25)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(11)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(9)->toDateString(), // trễ 2 ngày
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Trả trễ 2 ngày.',
                ]);
                PhieuPhat::query()->create([
                    'phieu_muon_id' => $pm2->id,
                    'user_id' => $u->id,
                    'nguoi_thu_id' => $admin->id,
                    'so_ngay_qua_han' => 2,
                    'tien_phat_moi_ngay' => 3000,
                    'tong_tien_phat' => 6000,
                    'ly_do' => 'Quá hạn mượn sách "Kafka bên bờ biển" (2 ngày. 2 ngày × 3.000đ = 6.000đ)',
                    'trang_thai' => PhieuPhat::TRANG_THAI_DA_NOP,
                    'ngay_nop' => $now->copy()->subDays(9)->toDateTimeString(),
                    'ghi_chu' => 'Chuyển khoản VietQR thành công.',
                ]);
            }
        }

        // 8. Bùi Văn Toàn (vipham.quahan@gmail.com) -> 🔴 Vi phạm (Quá hạn 8 ngày: 5 - 20 ngày)
        if ($u = $users->get('vipham.quahan@gmail.com')) {
            if ($s = $saches->get('Tôi thấy hoa vàng trên cỏ xanh')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(22)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(8)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'qua_han',
                    'ghi_chu' => 'Quá hạn 8 ngày chưa trả sách, hệ thống đã tạm chặn quyền mượn sách mới.',
                ]);
            }
        }

        // 9. Lý Thị Thúy (vipham.nophat@gmail.com) -> 🔴 Vi phạm (Nợ phiếu phạt chưa thanh toán)
        if ($u = $users->get('vipham.nophat@gmail.com')) {
            if ($s = $saches->get('Cô gái đến từ hôm qua')) {
                $pm = PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(30)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(16)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(8)->toDateString(), // trễ 8 ngày
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Độc giả đã trả sách tại quầy, còn nợ tiền phạt.',
                ]);
                PhieuPhat::query()->create([
                    'phieu_muon_id' => $pm->id,
                    'user_id' => $u->id,
                    'nguoi_thu_id' => null,
                    'so_ngay_qua_han' => 8,
                    'tien_phat_moi_ngay' => 3000,
                    'tong_tien_phat' => 24000,
                    'ly_do' => 'Quá hạn mượn sách "Cô gái đến từ hôm qua" (8 ngày. 8 ngày × 3.000đ = 24.000đ)',
                    'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
                    'ngay_nop' => null,
                    'ghi_chu' => 'Độc giả hẹn nộp tiền mặt vào cuối tuần.',
                ]);
            }
        }

        // 10. Ngô Văn Long (vipham.tre15ngay@gmail.com) -> 🔴 Vi phạm (Quá hạn 15 ngày)
        if ($u = $users->get('vipham.tre15ngay@gmail.com')) {
            if ($s = $saches->get('Gió đầu mùa')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(29)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(15)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'qua_han',
                    'ghi_chu' => 'Đang giữ sách quá hạn 15 ngày, gửi thông báo đôn đốc thu hồi.',
                ]);
            }
        }

        // 11. Trịnh Văn Sơn (cammuon.tre25ngay@gmail.com) -> ⛔ Cấm mượn (Quá hạn 25 ngày > 20 ngày)
        if ($u = $users->get('cammuon.tre25ngay@gmail.com')) {
            if ($s = $saches->get('Hà Nội băm sáu phố phường')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(39)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(25)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'qua_han',
                    'ghi_chu' => 'Quá hạn 25 ngày chưa hoàn trả, vi phạm quy định thư viện > 20 ngày.',
                ]);
            }
        }

        // 12. Phan Văn Dũng (cammuon.traqua20ngay@gmail.com) -> ⛔ Cấm mượn (Đã trả trễ 23 ngày - Phát sinh phạt lũy tiến & khóa)
        if ($u = $users->get('cammuon.traqua20ngay@gmail.com')) {
            if ($s = $saches->get('Đời thừa')) {
                $pm = PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s->id,
                    'ngay_muon' => $now->copy()->subDays(45)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(31)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(8)->toDateString(), // trễ 23 ngày
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Đã trả sách sau khi trễ 23 ngày. Chờ xử lý nộp phạt và xem xét mở khóa.',
                ]);

                // 23 ngày quá hạn: 10 ngày đầu * 3k + 13 ngày sau * 5k = 30k + 65k = 95.000đ
                PhieuPhat::query()->create([
                    'phieu_muon_id' => $pm->id,
                    'user_id' => $u->id,
                    'nguoi_thu_id' => null,
                    'so_ngay_qua_han' => 23,
                    'tien_phat_moi_ngay' => 5000,
                    'tong_tien_phat' => 95000,
                    'ly_do' => 'Quá hạn mượn sách "Đời thừa" (23 ngày. 10 ngày đầu (30.000đ) + 13 ngày sau × 5.000đ = 95.000đ)',
                    'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
                    'ngay_nop' => null,
                    'ghi_chu' => 'Chưa thanh toán phạt quá hạn 23 ngày. Tài khoản đang trong diện cấm mượn.',
                ]);
            }
        }

        // 13. Nguyễn Trọng Mạnh (manh27062005@gmail.com) -> Độc giả Demo (1 đã trả sớm, 1 đang mượn trong hạn)
        if ($u = $users->get('manh27062005@gmail.com')) {
            if ($s1 = $saches->get('Lão Hạc')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s1->id,
                    'ngay_muon' => $now->copy()->subDays(20)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->subDays(6)->toDateString(),
                    'ngay_tra_thuc_te' => $now->copy()->subDays(8)->toDateString(), // sớm 2 ngày
                    'trang_thai' => 'da_tra',
                    'ghi_chu' => 'Trả sách sớm, giữ sách cẩn thận.',
                ]);
            }

            // Sách số lượng thấp: "Tiểu thuyết hiếm: Bản dịch giới hạn" (so_luong = 1)
            // Khi đang mượn thì sách này sẽ có so_luong_con_lai = 0 (hết hàng, kiểm thử out-of-stock)
            if ($s2 = $saches->get('Tiểu thuyết hiếm: Bản dịch giới hạn')) {
                PhieuMuon::query()->create([
                    'user_id' => $u->id,
                    'sach_id' => $s2->id,
                    'ngay_muon' => $now->copy()->subDays(2)->toDateString(),
                    'ngay_tra_du_kien' => $now->copy()->addDays(12)->toDateString(),
                    'ngay_tra_thuc_te' => null,
                    'trang_thai' => 'dang_muon',
                    'ghi_chu' => 'Mượn bản in đặc biệt giới hạn 1 cuốn.',
                ]);
            }
        }
    }
}
