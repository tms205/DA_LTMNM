<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TheLoaiControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_creates_updates_and_deletes_empty_category(): void
    {
        $admin = User::factory()->admin()->create();

        $createResponse = $this->actingAs($admin)->post(route('admin.the-loais.store'), [
            'ten_the_loai' => 'Văn học hiện đại',
            'mo_ta' => 'Thể loại thử nghiệm.',
        ]);
        $theLoai = TheLoai::query()->where('ten_the_loai', 'Văn học hiện đại')->firstOrFail();

        $createResponse->assertRedirect(route('admin.the-loais.show', $theLoai));
        $this->assertModelExists($theLoai);

        $updateResponse = $this->actingAs($admin)->put(route('admin.the-loais.update', $theLoai), [
            'ten_the_loai' => 'Văn học Việt Nam',
            'mo_ta' => 'Đã cập nhật.',
        ]);

        $updateResponse->assertRedirect(route('admin.the-loais.show', $theLoai));
        $this->assertDatabaseHas('the_loais', [
            'id' => $theLoai->id,
            'ten_the_loai' => 'Văn học Việt Nam',
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('admin.the-loais.destroy', $theLoai));

        $deleteResponse->assertRedirect(route('admin.the-loais.index'));
        $this->assertModelMissing($theLoai);
    }

    public function test_admin_cannot_delete_category_that_contains_books(): void
    {
        $admin = User::factory()->admin()->create();
        $theLoai = TheLoai::factory()->create();
        Sach::factory()->for($theLoai, 'theLoai')->create();

        $response = $this->actingAs($admin)->from(route('admin.the-loais.show', $theLoai))
            ->delete(route('admin.the-loais.destroy', $theLoai));

        $response->assertRedirect(route('admin.the-loais.show', $theLoai))
            ->assertSessionHas('error');
        $this->assertModelExists($theLoai);
    }

    public function test_duplicate_category_name_returns_validation_error(): void
    {
        $admin = User::factory()->admin()->create();
        TheLoai::factory()->create(['ten_the_loai' => 'Văn học']);

        $response = $this->actingAs($admin)->post(route('admin.the-loais.store'), [
            'ten_the_loai' => 'Văn học',
        ]);

        $response->assertSessionHasErrors('ten_the_loai');
        $this->assertDatabaseCount('the_loais', 1);
    }
}
