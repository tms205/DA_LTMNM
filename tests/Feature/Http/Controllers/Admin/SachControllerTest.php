<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SachControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_creates_book_with_cover_and_authors(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $theLoai = TheLoai::factory()->create();
        $tacGia = TacGia::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.saches.store'), [
            'ten_sach' => 'Dế Mèn phiêu lưu ký',
            'the_loai_id' => $theLoai->id,
            'tac_gia_ids' => [$tacGia->id],
            'nam_xuat_ban' => 2021,
            'nha_xuat_ban' => 'NXB Kim Đồng',
            'so_luong' => 5,
            'hinh_anh' => UploadedFile::fake()->image('de-men.jpg', 600, 800)->size(500),
            'mo_ta' => 'Tác phẩm dành cho thiếu nhi.',
        ]);

        $sach = Sach::query()->where('ten_sach', 'Dế Mèn phiêu lưu ký')->firstOrFail();

        $response->assertRedirect(route('admin.saches.show', $sach));
        $this->assertDatabaseHas('saches', [
            'id' => $sach->id,
            'so_luong' => 5,
        ]);
        $this->assertDatabaseHas('sach_tac_gia', [
            'sach_id' => $sach->id,
            'tac_gia_id' => $tacGia->id,
        ]);
        Storage::disk('public')->assertExists($sach->hinh_anh);
    }

    public function test_invalid_book_payload_returns_field_errors_without_creating_book(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.saches.store'), []);

        $response->assertSessionHasErrors(['ten_sach', 'the_loai_id', 'so_luong', 'tac_gia_ids']);
        $this->assertDatabaseCount('saches', 0);
    }

    public function test_cover_rejects_non_image_upload(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $theLoai = TheLoai::factory()->create();
        $tacGia = TacGia::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.saches.store'), [
            'ten_sach' => 'Sách thử nghiệm',
            'the_loai_id' => $theLoai->id,
            'tac_gia_ids' => [$tacGia->id],
            'so_luong' => 3,
            'hinh_anh' => UploadedFile::fake()->create('payload.php', 10, 'application/x-php'),
        ]);

        $response->assertSessionHasErrors('hinh_anh');
        $this->assertDatabaseCount('saches', 0);
    }
}
