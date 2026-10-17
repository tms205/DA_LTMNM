<?php

namespace Database\Seeders;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class SachSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $theLoais = TheLoai::query()->get()->keyBy('ten_the_loai');
        $tacGias = TacGia::query()->get()->keyBy('ten_tac_gia');

        if ($theLoais->isEmpty() || $tacGias->isEmpty()) {
            throw new RuntimeException('Hãy chạy TheLoaiSeeder và TacGiaSeeder trước SachSeeder.');
        }

        $saches = [
            // Văn học Việt Nam
            ['ten_sach' => 'Truyện Kiều', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nguyễn Du'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 10],
            ['ten_sach' => 'Chí Phèo', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nam Cao'], 'nam_xuat_ban' => 2018, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 8],
            ['ten_sach' => 'Lão Hạc', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nam Cao'], 'nam_xuat_ban' => 2019, 'nha_xuat_ban' => 'NXB Kim Đồng', 'so_luong' => 6],
            ['ten_sach' => 'Đời thừa', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nam Cao'], 'nam_xuat_ban' => 2016, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 5],
            ['ten_sach' => 'Sống mòn', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nam Cao'], 'nam_xuat_ban' => 2017, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 4],
            ['ten_sach' => 'Dế Mèn phiêu lưu ký', 'the_loai' => 'Thiếu nhi & Tuổi trẻ', 'tac_gias' => ['Tô Hoài'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Kim Đồng', 'so_luong' => 12],
            ['ten_sach' => 'Vợ chồng A Phủ', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Tô Hoài'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 7],
            ['ten_sach' => 'Số Đỏ', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Vũ Trọng Phụng'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 6],
            ['ten_sach' => 'Giông Tố', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Vũ Trọng Phụng'], 'nam_xuat_ban' => 2019, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 5],
            ['ten_sach' => 'Tắt Đèn', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Ngô Tất Tố'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'NXB Hội Nhà Văn', 'so_luong' => 6],
            ['ten_sach' => 'Gió đầu mùa', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Thạch Lam'], 'nam_xuat_ban' => 2018, 'nha_xuat_ban' => 'NXB Trẻ', 'so_luong' => 5],
            ['ten_sach' => 'Hà Nội băm sáu phố phường', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Thạch Lam'], 'nam_xuat_ban' => 2019, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 4],
            ['ten_sach' => 'Mắt biếc', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nguyễn Nhật Ánh'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Trẻ', 'so_luong' => 8],
            ['ten_sach' => 'Cho tôi xin một vé đi tuổi thơ', 'the_loai' => 'Thiếu nhi & Tuổi trẻ', 'tac_gias' => ['Nguyễn Nhật Ánh'], 'nam_xuat_ban' => 2018, 'nha_xuat_ban' => 'NXB Trẻ', 'so_luong' => 7],
            ['ten_sach' => 'Tôi thấy hoa vàng trên cỏ xanh', 'the_loai' => 'Thiếu nhi & Tuổi trẻ', 'tac_gias' => ['Nguyễn Nhật Ánh'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'NXB Trẻ', 'so_luong' => 6],
            ['ten_sach' => 'Cô gái đến từ hôm qua', 'the_loai' => 'Thiếu nhi & Tuổi trẻ', 'tac_gias' => ['Nguyễn Nhật Ánh'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Trẻ', 'so_luong' => 5],

            // Tâm lý & Kỹ năng sống
            ['ten_sach' => 'Đắc Nhân Tâm', 'the_loai' => 'Tâm lý & Kỹ năng sống', 'tac_gias' => ['Dale Carnegie'], 'nam_xuat_ban' => 2022, 'nha_xuat_ban' => 'NXB Tổng hợp TP.HCM', 'so_luong' => 15],
            ['ten_sach' => 'Quẳng gánh lo đi và vui sống', 'the_loai' => 'Tâm lý & Kỹ năng sống', 'tac_gias' => ['Dale Carnegie'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Tổng hợp TP.HCM', 'so_luong' => 8],

            // Văn học nước ngoài
            ['ten_sach' => 'Rừng Na Uy', 'the_loai' => 'Văn học nước ngoài', 'tac_gias' => ['Haruki Murakami'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'NXB Hội Nhà Văn', 'so_luong' => 6],
            ['ten_sach' => 'Kafka bên bờ biển', 'the_loai' => 'Văn học nước ngoài', 'tac_gias' => ['Haruki Murakami'], 'nam_xuat_ban' => 2019, 'nha_xuat_ban' => 'NXB Hội Nhà Văn', 'so_luong' => 4],
            ['ten_sach' => 'Hoàng tử bé', 'the_loai' => 'Thiếu nhi & Tuổi trẻ', 'tac_gias' => ['Antoine de Saint-Exupéry'], 'nam_xuat_ban' => 2022, 'nha_xuat_ban' => 'NXB Kim Đồng', 'so_luong' => 10],

            // Công nghệ thông tin
            ['ten_sach' => 'Clean Code: A Handbook of Agile Software Craftsmanship', 'the_loai' => 'Công nghệ thông tin', 'tac_gias' => ['Robert C. Martin'], 'nam_xuat_ban' => 2020, 'nha_xuat_ban' => 'Prentice Hall', 'so_luong' => 5],
            ['ten_sach' => 'Clean Architecture: A Craftsman\'s Guide', 'the_loai' => 'Công nghệ thông tin', 'tac_gias' => ['Robert C. Martin'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'Prentice Hall', 'so_luong' => 5],

            // Lịch sử & Triết học
            ['ten_sach' => 'Sapiens: Lược sử loài người', 'the_loai' => 'Lịch sử & Triết học', 'tac_gias' => ['Yuval Noah Harari'], 'nam_xuat_ban' => 2021, 'nha_xuat_ban' => 'NXB Tri Thức', 'so_luong' => 7],
            ['ten_sach' => 'Homo Deus: Lược sử tương lai', 'the_loai' => 'Lịch sử & Triết học', 'tac_gias' => ['Yuval Noah Harari'], 'nam_xuat_ban' => 2022, 'nha_xuat_ban' => 'NXB Tri Thức', 'so_luong' => 5],

            // Sách số lượng thấp (Low stock: 1 cuốn)
            ['ten_sach' => 'Tiểu thuyết hiếm: Bản dịch giới hạn', 'the_loai' => 'Văn học nước ngoài', 'tac_gias' => ['Haruki Murakami'], 'nam_xuat_ban' => 2015, 'nha_xuat_ban' => 'NXB Hội Nhà Văn', 'so_luong' => 1],

            // Sách đồng tác giả (Co-authors: Nam Cao & Tô Hoài trong tuyển tập)
            ['ten_sach' => 'Tuyển tập truyện ngắn hiện thực Việt Nam', 'the_loai' => 'Văn học Việt Nam', 'tac_gias' => ['Nam Cao', 'Tô Hoài'], 'nam_xuat_ban' => 2023, 'nha_xuat_ban' => 'NXB Văn Học', 'so_luong' => 4],
        ];

        foreach ($saches as $sachData) {
            $theLoai = $theLoais->get($sachData['the_loai']);
            $theLoaiId = $theLoai ? $theLoai->id : $theLoais->first()->id;

            $sach = Sach::query()->updateOrCreate(
                ['ten_sach' => $sachData['ten_sach']],
                [
                    'the_loai_id' => $theLoaiId,
                    'nam_xuat_ban' => $sachData['nam_xuat_ban'],
                    'nha_xuat_ban' => $sachData['nha_xuat_ban'],
                    'so_luong' => $sachData['so_luong'],
                ],
            );

            $tacGiaIds = [];
            foreach ($sachData['tac_gias'] as $tgName) {
                if ($tacGias->has($tgName)) {
                    $tacGiaIds[] = $tacGias->get($tgName)->id;
                }
            }

            if (! empty($tacGiaIds)) {
                $sach->tacGias()->sync($tacGiaIds);
            }
        }
    }
}
