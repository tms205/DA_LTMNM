<?php

namespace Database\Seeders;

use App\Models\TacGia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TacGiaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tacGias = [
            [
                'ten_tac_gia' => 'Nguyễn Du',
                'nam_sinh' => 1765,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Đại thi hào dân tộc, tác giả Truyện Kiều và Danh nhân văn hóa thế giới.',
            ],
            [
                'ten_tac_gia' => 'Nam Cao',
                'nam_sinh' => 1915,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Nhà văn hiện thực xuất sắc với các kiệt tác về người nông dân và trí thức nghèo như Chí Phèo, Lão Hạc.',
            ],
            [
                'ten_tac_gia' => 'Tô Hoài',
                'nam_sinh' => 1920,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Nhà văn có sự nghiệp sáng tác đồ sộ, nổi tiếng với Dế Mèn phiêu lưu ký và Vợ chồng A Phủ.',
            ],
            [
                'ten_tac_gia' => 'Vũ Trọng Phụng',
                'nam_sinh' => 1912,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Bậc thầy văn xuôi trào phúng hiện thực, tác giả của tiểu thuyết Số Đỏ, Giông Tố.',
            ],
            [
                'ten_tac_gia' => 'Ngô Tất Tố',
                'nam_sinh' => 1894,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Nhà văn, nhà báo, nhà văn hóa lớn với kiệt tác hiện thực phê phán Tắt Đèn.',
            ],
            [
                'ten_tac_gia' => 'Thạch Lam',
                'nam_sinh' => 1910,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Nhà văn tài hoa của nhóm Tự Lực Văn Đoàn, nổi tiếng với Gió đầu mùa, Hà Nội 36 phố phường.',
            ],
            [
                'ten_tac_gia' => 'Nguyễn Nhật Ánh',
                'nam_sinh' => 1955,
                'quoc_tich' => 'Việt Nam',
                'tieu_su' => 'Nhà văn đương đại hàng đầu viết cho thanh thiếu niên với Mắt biếc, Cho tôi xin một vé đi tuổi thơ.',
            ],
            [
                'ten_tac_gia' => 'Dale Carnegie',
                'nam_sinh' => 1888,
                'quoc_tich' => 'Hoa Kỳ',
                'tieu_su' => 'Tác giả nổi tiếng thế giới về nghệ thuật giao tiếp và phát triển bản thân với Đắc Nhân Tâm.',
            ],
            [
                'ten_tac_gia' => 'Haruki Murakami',
                'nam_sinh' => 1949,
                'quoc_tich' => 'Nhật Bản',
                'tieu_su' => 'Nhà văn hiện thực huyền ảo đương đại nổi tiếng thế giới, tác giả Rừng Na Uy, Kafka bên bờ biển.',
            ],
            [
                'ten_tac_gia' => 'Robert C. Martin',
                'nam_sinh' => 1952,
                'quoc_tich' => 'Hoa Kỳ',
                'tieu_su' => 'Kỹ sư phần mềm kỳ cựu (Uncle Bob), tác giả bộ sách kinh điển Clean Code, Clean Architecture.',
            ],
            [
                'ten_tac_gia' => 'Yuval Noah Harari',
                'nam_sinh' => 1976,
                'quoc_tich' => 'Israel',
                'tieu_su' => 'Nhà sử học, triết gia nổi tiếng toàn cầu với Sapiens: Lược sử loài người và Homo Deus.',
            ],
            [
                'ten_tac_gia' => 'Antoine de Saint-Exupéry',
                'nam_sinh' => 1900,
                'quoc_tich' => 'Pháp',
                'tieu_su' => 'Nhà văn và phi công người Pháp, tác giả cuốn sách thiếu nhi kinh điển toàn cầu Hoàng tử bé.',
            ],
        ];

        foreach ($tacGias as $tacGia) {
            TacGia::query()->updateOrCreate(
                ['ten_tac_gia' => $tacGia['ten_tac_gia']],
                $tacGia,
            );
        }
    }
}
