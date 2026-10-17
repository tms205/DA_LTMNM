<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');
        $secret = 'JBSWY3DPEHPK3PXP';

        $users = [
            // Quản trị viên & Thủ thư
            [
                'name' => 'Thủ thư Quản trị',
                'email' => 'admin@gmail.com',
                'so_dien_thoai' => '0901234567',
                'dia_chi' => '140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP. HCM',
                'ngay_sinh' => '1992-05-15',
                'role' => 'admin',
                'ghi_chu_thu_thu' => null,
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Nguyễn Thị Bích Thủy (Thủ thư quầy)',
                'email' => 'thuthu@thuvien.vn',
                'so_dien_thoai' => '0909888999',
                'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
                'ngay_sinh' => '1995-10-20',
                'role' => 'admin',
                'ghi_chu_thu_thu' => null,
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // Độc giả demo OTP
            [
                'name' => 'Nguyễn Trọng Mạnh (Demo OTP)',
                'email' => 'manh27062005@gmail.com',
                'so_dien_thoai' => '0769479813',
                'dia_chi' => '297 Phan Huy Ích, P. 14, Q. Gò Vấp, TP. HCM',
                'ngay_sinh' => '2005-06-27',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Độc giả nhiệt tình tham gia câu lạc bộ sách',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // 1. TIER: 🌟 Uy tín cao (Trong sạch) - Trả sớm >= 3 lần, 0 nợ phạt
            [
                'name' => 'Lê Hoàng Uy Tín',
                'email' => 'uytin.lehoang@gmail.com',
                'so_dien_thoai' => '0912111222',
                'dia_chi' => 'Số 12 Điện Biên Phủ, P. 15, Q. Bình Thạnh, TP. HCM',
                'ngay_sinh' => '2001-04-12',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Độc giả gương mẫu, đọc rất nhanh và luôn giữ gìn sách cẩn thận',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Trần Thị Minh Châu',
                'email' => 'uytin.tranminh@gmail.com',
                'so_dien_thoai' => '0913222333',
                'dia_chi' => 'Chung cư Sunrise City, Q. 7, TP. HCM',
                'ngay_sinh' => '2002-09-18',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Độc giả quen thuộc tại phòng đọc nghiên cứu',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // 2. TIER: 🟢 Bình thường (Đủ điều kiện) - Mượn trả đúng hạn hoặc độc giả mới
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'binhthuong.nguyenvan@gmail.com',
                'so_dien_thoai' => '0914333444',
                'dia_chi' => '45 Nguyễn Thị Minh Khai, Q. 1, TP. HCM',
                'ngay_sinh' => '2003-02-14',
                'role' => 'user',
                'ghi_chu_thu_thu' => null,
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Phạm Thị Mai',
                'email' => 'binhthuong.phamthi@gmail.com',
                'so_dien_thoai' => '0915444555',
                'dia_chi' => '88 Võ Văn Tần, Q. 3, TP. HCM',
                'ngay_sinh' => '2004-07-22',
                'role' => 'user',
                'ghi_chu_thu_thu' => null,
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Đặng Quốc Huy (Mới đăng ký)',
                'email' => 'binhthuong.moitoanh@gmail.com',
                'so_dien_thoai' => '0916555666',
                'dia_chi' => 'Ký túc xá Đại học Bách Khoa, TP. Thủ Đức',
                'ngay_sinh' => '2006-01-05',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Độc giả mới làm thẻ, đã hướng dẫn sử dụng hệ thống',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // 3. TIER: 🟡 Cần chú ý (Nhắc nhở nhẹ) - Trễ 1-4 ngày HOẶC giữ >= 3 cuốn HOẶC có tiền sử trễ
            [
                'name' => 'Hoàng Đức Nam (Trễ 2 ngày)',
                'email' => 'canhbao.trenhe@gmail.com',
                'so_dien_thoai' => '0917666777',
                'dia_chi' => '20 Cộng Hòa, P. 4, Q. Tân Bình, TP. HCM',
                'ngay_sinh' => '2002-12-30',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Đã gọi điện nhắc hạn trả sách, độc giả hứa cuối tuần ghé trả',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Đỗ Thị Hồng (Giữ cùng lúc 3 cuốn)',
                'email' => 'canhbao.nhieusach@gmail.com',
                'so_dien_thoai' => '0918777888',
                'dia_chi' => '102 Hoàng Văn Thụ, Q. Phú Nhuận, TP. HCM',
                'ngay_sinh' => '2003-08-10',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Sinh viên làm đồ án tốt nghiệp, mượn nhiều đầu sách tham khảo',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Vũ Hải Đăng (Tiền sử trễ hạn)',
                'email' => 'canhbao.tiensu@gmail.com',
                'so_dien_thoai' => '0919888999',
                'dia_chi' => '33 Lê Văn Sỹ, P. 13, Q. 3, TP. HCM',
                'ngay_sinh' => '2001-06-19',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Từng có 2 lần nộp phạt trễ hạn, cần kiểm tra hạn kỹ khi cho mượn tiếp',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // 4. TIER: 🔴 Vi phạm (Chặn mượn sách) - Quá hạn 5-20 ngày HOẶC còn nợ tiền phạt
            [
                'name' => 'Bùi Văn Toàn (Quá hạn 8 ngày)',
                'email' => 'vipham.quahan@gmail.com',
                'so_dien_thoai' => '0921000111',
                'dia_chi' => '54 Quang Trung, P. 10, Q. Gò Vấp, TP. HCM',
                'ngay_sinh' => '2000-03-25',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Đã gửi email cảnh báo quá hạn 8 ngày, tạm chặn quyền mượn',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Lý Thị Thúy (Còn nợ phiếu phạt)',
                'email' => 'vipham.nophat@gmail.com',
                'so_dien_thoai' => '0922111222',
                'dia_chi' => '17 Bạch Đằng, P. 2, Q. Tân Bình, TP. HCM',
                'ngay_sinh' => '2004-10-09',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Còn nợ 1 phiếu phạt quá hạn chưa thanh toán, cần nộp tại quầy',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Ngô Văn Long (Quá hạn 15 ngày)',
                'email' => 'vipham.tre15ngay@gmail.com',
                'so_dien_thoai' => '0923222333',
                'dia_chi' => '71 Cách Mạng Tháng 8, Q. 10, TP. HCM',
                'ngay_sinh' => '1999-11-17',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Quá hạn 15 ngày chưa đem sách trả, hệ thống tự động khóa quyền mượn mới',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],

            // 5. TIER: ⛔ Cấm mượn - Quá 20 ngày HOẶC bị khóa tài khoản
            [
                'name' => 'Trịnh Văn Sơn (Quá hạn 25 ngày)',
                'email' => 'cammuon.tre25ngay@gmail.com',
                'so_dien_thoai' => '0924333444',
                'dia_chi' => '205 Hùng Vương, P. 9, Q. 5, TP. HCM',
                'ngay_sinh' => '1998-05-04',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Quá hạn trên 20 ngày, vi phạm nghiêm trọng quy chế thư viện',
                'khoa_muon_sach' => false,
                'ly_do_khoa' => null,
            ],
            [
                'name' => 'Mai Thị Phượng (Thủ thư khóa quyền)',
                'email' => 'cammuon.thuthukhoa@gmail.com',
                'so_dien_thoai' => '0925444555',
                'dia_chi' => '12 Nguyễn Trãi, P. Bến Thành, Q. 1, TP. HCM',
                'ngay_sinh' => '2002-04-16',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Làm rách gáy và bôi bẩn sách quý hiếm, cố tình không bồi thường',
                'khoa_muon_sach' => true,
                'ly_do_khoa' => 'Vi phạm nội quy: Làm hỏng sách quý hiếm và từ chối bồi hoàn theo biên bản.',
            ],
            [
                'name' => 'Phan Văn Dũng (Trả trễ 23 ngày - Khóa mượn)',
                'email' => 'cammuon.traqua20ngay@gmail.com',
                'so_dien_thoai' => '0926555666',
                'dia_chi' => '300 Xô Viết Nghệ Tĩnh, Q. Bình Thạnh, TP. HCM',
                'ngay_sinh' => '2001-08-08',
                'role' => 'user',
                'ghi_chu_thu_thu' => 'Đã trả sách sau khi trễ 23 ngày, nợ phiếu phạt 95.000đ và bị tự động cấm mượn',
                'khoa_muon_sach' => true,
                'ly_do_khoa' => 'Tự động cấm mượn: Trả sách trễ 23 ngày (vượt quá 20 ngày quy định).',
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'so_dien_thoai' => $userData['so_dien_thoai'],
                    'dia_chi' => $userData['dia_chi'],
                    'ngay_sinh' => $userData['ngay_sinh'],
                    'password' => $password,
                    'role' => $userData['role'],
                    'google2fa_secret' => $secret,
                    'ghi_chu_thu_thu' => $userData['ghi_chu_thu_thu'],
                    'khoa_muon_sach' => $userData['khoa_muon_sach'],
                    'ly_do_khoa' => $userData['ly_do_khoa'],
                ],
            );
        }
    }
}
