<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_dashboard_renders_book_categories_for_recent_loans(): void
    {
        $admin = User::factory()->admin()->create();
        $reader = User::factory()->docGia()->create();
        $theLoai = TheLoai::factory()->create(['ten_the_loai' => 'Văn học']);
        $saches = Sach::factory()->count(2)->for($theLoai, 'theLoai')->create();

        foreach ($saches as $sach) {
            PhieuMuon::factory()->for($reader)->for($sach, 'sach')->create();
        }

        $response = $this->actingAs($admin)->get(route('home'));

        $response->assertOk()
            ->assertSeeText('Văn học');
    }

    public function test_reader_dashboard_renders_book_categories_for_recent_loans(): void
    {
        $reader = User::factory()->docGia()->create();
        $theLoai = TheLoai::factory()->create(['ten_the_loai' => 'Thiếu nhi']);
        $saches = Sach::factory()->count(2)->for($theLoai, 'theLoai')->create();

        foreach ($saches as $sach) {
            PhieuMuon::factory()->for($reader)->for($sach, 'sach')->create();
        }

        $response = $this->actingAs($reader)->get(route('home'));

        $response->assertOk()
            ->assertSeeText('Thiếu nhi');
    }
}
