<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Sach;
use Illuminate\Contracts\Console\Kernel;

$booksToProcess = [
    1 => [
        'name' => 'Truyện Kiều',
        'url' => 'https://salt.tikicdn.com/ts/product/c0/11/9c/6c0242765c74c23fe5b870772343da06.png',
        'file' => 'sach_1_truyen_kieu.png',
        'source' => 'https://tiki.vn/nguyen-du-truyen-thuy-kieu-ban-dac-biet-bia-mem-tai-ban-p195961281.html',
    ],
    2 => [
        'name' => 'Chí Phèo',
        'url' => 'https://salt.tikicdn.com/ts/product/65/ba/6a/cb4635a9e211a46077053b817b359a58.png',
        'file' => 'sach_2_chi_pheo.png',
        'source' => 'https://tiki.vn/chi-pheo-hh-hh-p278321314.html',
    ],
    3 => [
        'name' => 'Lão Hạc',
        'url' => 'https://salt.tikicdn.com/ts/product/05/d8/b8/878811cc941c663688a7a125260be313.jpg',
        'file' => 'sach_3_lao_hac.jpg',
        'source' => 'https://tiki.vn/nam-cao-lao-hac-tai-ban-p205802242.html',
    ],
    4 => [
        'name' => 'Đời thừa',
        'url' => 'https://salt.tikicdn.com/ts/product/c2/2b/c0/3a3546aecb29e534a7f5a37a9c96d04b.jpeg',
        'file' => 'sach_4_doi_thua.jpeg',
        'source' => 'https://tiki.vn/sach-doi-thua-nam-cao-sbooks-p274356733.html',
    ],
    5 => [
        'name' => 'Sống mòn',
        'url' => 'https://salt.tikicdn.com/ts/product/83/1a/06/6272fee7d673b6c155222d2c72f7212d.jpg',
        'file' => 'sach_5_song_mon.jpg',
        'source' => 'https://tiki.vn/sach-nam-cao-song-mon-bia-cung-tai-ban-p162100197.html',
    ],
    6 => [
        'name' => 'Dế Mèn phiêu lưu ký',
        'url' => 'https://salt.tikicdn.com/ts/product/70/4a/98/90a8e9f3276ab4b84165e9fc12d9a0a1.jpg',
        'file' => 'sach_6_de_men_phieu_luu_ky.jpg',
        'source' => 'https://tiki.vn/sach-de-men-phieu-luu-ky-dau-dua-minh-hoa-an-ban-ki-niem-100-nam-to-hoai-p68330624.html',
    ],
    7 => [
        'name' => 'Vợ chồng A Phủ',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1640952539i/59984375.jpg',
        'file' => 'sach_7_vo_chong_a_phu.jpg',
        'source' => 'https://www.goodreads.com/book/show/59984375-v-ch-ng-a-ph-',
    ],
    8 => [
        'name' => 'O chuột',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1395348801i/21523734.jpg',
        'file' => 'sach_8_o_chuot.jpg',
        'source' => 'https://www.goodreads.com/book/show/21523734-o-chuot',
    ],
    9 => [
        'name' => 'Cát bụi chân ai',
        'url' => 'https://salt.tikicdn.com/ts/product/b2/2f/db/937261e2b589aca2c09cc7c5b60d3765.jpg',
        'file' => 'sach_9_cat_bui_chan_ai.jpg',
        'source' => 'https://tiki.vn/cat-bui-chan-ai-to-hoai-thao-nguyen-p277975957.html',
    ],
    10 => [
        'name' => 'Thơ thơ',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1545381828i/22469995.jpg',
        'file' => 'sach_10_tho_tho.jpg',
        'source' => 'https://www.goodreads.com/book/show/22469995-th-th',
    ],
    11 => [
        'name' => 'Gửi hương cho gió',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1545382605i/22469997.jpg',
        'file' => 'sach_11_gui_huong_cho_gio.jpg',
        'source' => 'https://www.goodreads.com/book/show/22469997-g-i-h-ng-cho-gi',
    ],
    13 => [
        'name' => 'Mắt biếc',
        'url' => 'https://salt.tikicdn.com/ts/product/0a/ed/df/6a3584cbef4ed5c6517059e07d48a63c.jpg',
        'file' => 'sach_13_mat_biec.jpg',
        'source' => 'https://tiki.vn/mat-biec-nguyen-nhat-anh-p266552447.html',
    ],
    14 => [
        'name' => 'Cô gái đến từ hôm qua',
        'url' => 'https://salt.tikicdn.com/ts/product/92/b9/15/687fddc6ed00d35d3598661cd92a22d3.jpg',
        'file' => 'sach_14_co_gai_den_tu_hom_qua.jpg',
        'source' => 'https://tiki.vn/sach-co-gai-den-tu-hom-qua-phien-ban-dac-biet-nguyen-nhat-anh-p277938215.html',
    ],
    15 => [
        'name' => 'Kính vạn hoa',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1366161323i/17826150.jpg',
        'file' => 'sach_15_kinh_van_hoa.jpg',
        'source' => 'https://www.goodreads.com/book/show/17826150-k-nh-v-n-hoa',
    ],
    16 => [
        'name' => 'Cho tôi xin một vé đi tuổi thơ',
        'url' => 'https://salt.tikicdn.com/ts/product/83/9b/a0/66221bb18fb7af6d853b9185fd6bfb90.jpg',
        'file' => 'sach_16_cho_toi_xin_mot_ve_di_tuoi_tho.jpg',
        'source' => 'https://tiki.vn/truyen-cua-nguyen-nhat-anh-cho-toi-xin-mot-ve-di-tuoi-tho-p271199948.html',
    ],
    17 => [
        'name' => 'Tôi thấy hoa vàng trên cỏ xanh',
        'url' => 'https://salt.tikicdn.com/ts/product/ac/92/49/3384968eac3888e307a84118b424c1d4.jpg',
        'file' => 'sach_17_toi_thay_hoa_vang_tren_co_xanh.jpg',
        'source' => 'https://tiki.vn/toi-thay-hoa-vang-tren-co-xanh-top-nhung-cuon-sach-ban-chay-cua-nguyen-nhat-anh-tang-kem-postcard-green-life-p33645200.html',
    ],
    18 => [
        'name' => 'Ngồi khóc trên cây',
        'url' => 'https://salt.tikicdn.com/ts/product/75/28/5f/939fcb6fbf07395a57c6d47a66ed56ce.png',
        'file' => 'sach_18_ngoi_khoc_tren_cay.png',
        'source' => 'https://tiki.vn/nguyen-nhat-anh-ngoi-khoc-tren-cay-in-lan-thu-29-p278610389.html',
    ],
    19 => [
        'name' => 'Con chó nhỏ mang giỏ hoa hồng',
        'url' => 'https://salt.tikicdn.com/ts/product/0d/db/08/41780403097cce741c827f710a828c8c.jpg',
        'file' => 'sach_19_con_cho_nho_mang_gio_hoa_hong.jpg',
        'source' => 'https://tiki.vn/sach-con-cho-nho-mang-gio-hoa-hong-nguyen-nhat-anh-p129138393.html',
    ],
    22 => [
        'name' => 'Số Đỏ',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1630593688i/15847487.jpg',
        'file' => 'sach_22_so_do.jpg',
        'source' => 'https://www.goodreads.com/book/show/15847487-s-đỏ',
    ],
    23 => [
        'name' => 'Giông Tố',
        'url' => 'https://salt.tikicdn.com/ts/product/06/c5/b0/afe29f92ee65cde579f66ac6f162f0f6.jpg',
        'file' => 'sach_23_giong_to.jpg',
        'source' => 'https://tiki.vn/giong-to-vu-trong-phung-p263563188.html',
    ],
    24 => [
        'name' => 'Tắt Đèn',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1479993956i/13147425.jpg',
        'file' => 'sach_24_tat_den.jpg',
        'source' => 'https://www.goodreads.com/book/show/13147425-t-t-den',
    ],
    25 => [
        'name' => 'Gió đầu mùa',
        'url' => 'https://salt.tikicdn.com/ts/product/c7/5a/85/c6a1b857f072ac7b2348a96b84112d01.jpg',
        'file' => 'sach_25_gio_dau_mua.jpg',
        'source' => 'https://tiki.vn/sach-gio-lanh-dau-mua-bia-mem-thach-lam-minh-thang-p277429417.html',
    ],
    26 => [
        'name' => 'Hà Nội băm sáu phố phường',
        'url' => 'https://salt.tikicdn.com/ts/product/22/d4/6f/5c2b29075aa74ce294ac25442b0840e8.jpg',
        'file' => 'sach_26_ha_noi_bam_sau_pho_phuong.jpg',
        'source' => 'https://tiki.vn/ha-noi-bam-sau-pho-phuong-tu-sach-van-hoc-trong-nha-truong-thach-lam-kim-dong-p278600448.html',
    ],
    27 => [
        'name' => 'Đắc Nhân Tâm',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1611727559i/56855059.jpg',
        'file' => 'sach_27_dac_nhan_tam.jpg',
        'source' => 'https://www.goodreads.com/book/show/56855059-c-nh-n-t-m',
    ],
    28 => [
        'name' => 'Quẳng gánh lo đi và vui sống',
        'url' => 'https://covers.openlibrary.org/b/id/10829208-L.jpg',
        'file' => 'sach_28_quang_ganh_lo_di.jpg',
        'source' => 'https://openlibrary.org/works/OL1963054W/How_to_Stop_Worrying_and_Start_Living',
    ],
    29 => [
        'name' => 'Rừng Na Uy',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1630905114i/12134351.jpg',
        'file' => 'sach_29_rung_na_uy.jpg',
        'source' => 'https://www.goodreads.com/book/show/12134351',
    ],
    30 => [
        'name' => 'Kafka bên bờ biển',
        'url' => 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1310381217i/12009048.jpg',
        'file' => 'sach_30_kafka_ben_bo_bien.jpg',
        'source' => 'https://www.goodreads.com/book/show/12009048',
    ],
    31 => [
        'name' => 'Hoàng tử bé',
        'url' => 'https://covers.openlibrary.org/b/id/10746692-L.jpg',
        'file' => 'sach_31_hoang_tu_be.jpg',
        'source' => 'https://openlibrary.org/works/OL15183344W/The_Little_Prince',
    ],
    32 => [
        'name' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
        'url' => 'https://covers.openlibrary.org/b/id/8065615-L.jpg',
        'file' => 'sach_32_clean_code.jpg',
        'source' => 'https://openlibrary.org/works/OL15157143W/Clean_Code',
    ],
    33 => [
        'name' => 'Clean Architecture: A Craftsman\'s Guide',
        'url' => 'https://covers.openlibrary.org/b/id/8605114-L.jpg',
        'file' => 'sach_33_clean_architecture.jpg',
        'source' => 'https://openlibrary.org/works/OL17855018W/Clean_Architecture',
    ],
    34 => [
        'name' => 'Sapiens: Lược sử loài người',
        'url' => 'https://covers.openlibrary.org/b/id/8634250-L.jpg',
        'file' => 'sach_34_sapiens_luoc_su_loai_nguoi.jpg',
        'source' => 'https://openlibrary.org/works/OL17075727W/Sapiens',
    ],
    35 => [
        'name' => 'Homo Deus: Lược sử tương lai',
        'url' => 'https://covers.openlibrary.org/b/id/8846275-L.jpg',
        'file' => 'sach_35_homo_deus.jpg',
        'source' => 'https://openlibrary.org/works/OL17358784W/Homo_Deus',
    ],
];

$targetDir = realpath(__DIR__.'/../storage/app/public/sach-bia');
if (! $targetDir) {
    mkdir(__DIR__.'/../storage/app/public/sach-bia', 0755, true);
    $targetDir = realpath(__DIR__.'/../storage/app/public/sach-bia');
}

echo '=== STARTING DOWNLOAD & UPDATE OF COVERS ==='.PHP_EOL;
echo 'Target directory: '.$targetDir.PHP_EOL.PHP_EOL;

$results = [];

foreach ($booksToProcess as $id => $info) {
    $sach = Sach::find($id);
    if (! $sach) {
        echo "[-] Book ID {$id} not found in database!".PHP_EOL;

        continue;
    }

    $destPath = $targetDir.DIRECTORY_SEPARATOR.$info['file'];

    // Download image using curl
    $ch = curl_init($info['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 || empty($data)) {
        echo "[-] Failed downloading ID {$id} ({$info['name']}): HTTP {$httpCode}, Error: {$curlError}".PHP_EOL;

        continue;
    }

    // Validate image format
    $imageInfo = @getimagesizefromstring($data);
    if ($imageInfo === false) {
        echo "[-] Invalid image data for ID {$id} ({$info['name']})".PHP_EOL;

        continue;
    }

    // Save image
    file_put_contents($destPath, $data);

    // Update database
    $dbPath = 'sach-bia/'.$info['file'];
    $sach->hinh_anh = $dbPath;
    $sach->save();

    $sizeKb = round(strlen($data) / 1024, 1);
    $dim = "{$imageInfo[0]}x{$imageInfo[1]}";
    echo "[+] SUCCESS ID {$id}: '{$info['name']}' -> {$dbPath} ({$sizeKb} KB, {$dim})".PHP_EOL;

    $results[$id] = [
        'id' => $id,
        'name' => $sach->ten_sach,
        'source' => $info['source'],
        'abs_path' => $destPath,
        'db_path' => $dbPath,
        'size_kb' => $sizeKb,
        'dim' => $dim,
    ];
}

echo PHP_EOL.'Total processed and updated: '.count($results).' / '.count($booksToProcess).PHP_EOL;
file_put_contents(__DIR__.'/cover_results.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
