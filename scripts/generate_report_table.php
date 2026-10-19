<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Sach;
use Illuminate\Contracts\Console\Kernel;

$resultsJson = json_decode(file_get_contents(__DIR__.'/cover_results.json'), true);

$allBooks = Sach::with(['tacGias', 'theLoai'])->orderBy('id')->get();

$reportData = [];

foreach ($allBooks as $book) {
    $id = $book->id;
    $title = $book->ten_sach;
    $authors = $book->tacGias->pluck('ten_tac_gia')->join(', ');

    if (isset($resultsJson[$id])) {
        $info = $resultsJson[$id];
        $absPath = $info['abs_path'];
        $dbPath = $info['db_path'];
        $source = $info['source'];
        $status = 'Thành công (Đã tải mới)';
    } elseif ($id == 20) {
        $absPath = realpath(__DIR__.'/../storage/app/public/'.$book->hinh_anh);
        $dbPath = $book->hinh_anh;
        $source = 'Ảnh gốc dự án (NXB Trẻ / Nội bộ thư viện)';
        $status = 'Thành công (Đã có sẵn)';
    } elseif ($id == 21) {
        $absPath = realpath(__DIR__.'/../storage/app/public/'.$book->hinh_anh);
        $dbPath = $book->hinh_anh;
        $source = 'Ảnh gốc dự án (Nội bộ thư viện)';
        $status = 'Thành công (Đã có sẵn)';
    } elseif ($id == 12) {
        $absPath = '— (Giữ mặc định)';
        $dbPath = '— (NULL)';
        $source = 'Không tìm được nguồn ảnh quét bìa tin cậy (Tập thơ 1960 của Xuân Diệu)';
        $status = 'Chưa tìm được ảnh';
    } elseif ($id == 36) {
        $absPath = '— (Giữ mặc định)';
        $dbPath = '— (NULL)';
        $source = 'Sách thử nghiệm hư cấu từ Seeder (Không tồn tại ấn bản thực tế)';
        $status = 'Chưa tìm được ảnh (Sách thử nghiệm)';
    } elseif ($id == 37) {
        $absPath = '— (Giữ mặc định)';
        $dbPath = '— (NULL)';
        $source = 'Sách thử nghiệm đồng tác giả từ Seeder (Không có ấn bản độc lập tương ứng)';
        $status = 'Chưa tìm được ảnh (Sách thử nghiệm)';
    } else {
        $absPath = '—';
        $dbPath = $book->hinh_anh ?? '—';
        $source = '—';
        $status = 'Chưa tìm được ảnh';
    }

    $reportData[] = [
        'id' => $id,
        'title' => $title,
        'authors' => $authors,
        'source' => $source,
        'abs_path' => $absPath,
        'db_path' => $dbPath,
        'status' => $status,
    ];
}

file_put_contents(__DIR__.'/final_table_data.json', json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Report generated for '.count($reportData).' books.'.PHP_EOL;
