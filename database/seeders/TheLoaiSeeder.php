<?php

namespace Database\Seeders;

use App\Models\TheLoai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TheLoaiSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $theLoais = [
            ['ten_the_loai' => 'Văn học Việt Nam', 'mo_ta' => 'Các kiệt tác văn học kinh điển và tác phẩm đương đại của các tác giả Việt Nam.'],
            ['ten_the_loai' => 'Văn học nước ngoài', 'mo_ta' => 'Tiểu thuyết, truyện ngắn dịch và tác phẩm kinh điển của văn học thế giới.'],
            ['ten_the_loai' => 'Công nghệ thông tin', 'mo_ta' => 'Sách lập trình, kiến trúc phần mềm, cơ sở dữ liệu, trí tuệ nhân tạo và kỹ thuật số.'],
            ['ten_the_loai' => 'Kinh tế & Kinh doanh', 'mo_ta' => 'Sách về quản trị kinh doanh, tài chính, đầu tư, khởi nghiệp và kinh tế học.'],
            ['ten_the_loai' => 'Tâm lý & Kỹ năng sống', 'mo_ta' => 'Sách phát triển bản thân, kỹ năng giao tiếp, quản lý cảm xúc và tư duy tích cực.'],
            ['ten_the_loai' => 'Lịch sử & Triết học', 'mo_ta' => 'Nghiên cứu về lịch sử các nền văn minh, lịch sử Việt Nam và tư tưởng triết học.'],
            ['ten_the_loai' => 'Khoa học tự nhiên', 'mo_ta' => 'Phổ biến kiến thức vật lý, vũ trụ học, sinh học, hóa học và khoa học thường thức.'],
            ['ten_the_loai' => 'Thiếu nhi & Tuổi trẻ', 'mo_ta' => 'Truyện tranh, văn học thanh thiếu nhi, bách khoa tri thức cho tuổi mới lớn.'],
        ];

        foreach ($theLoais as $theLoai) {
            TheLoai::query()->updateOrCreate(
                ['ten_the_loai' => $theLoai['ten_the_loai']],
                $theLoai,
            );
        }
    }
}
