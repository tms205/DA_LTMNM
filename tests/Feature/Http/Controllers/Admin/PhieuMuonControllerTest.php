<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PhieuMuonControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_creates_loan_when_book_is_available(): void
    {
        $this->travelTo('2026-09-29 09:00:00');
        $admin = User::factory()->admin()->create();
        $docGia = User::factory()->docGia()->create();
        $sach = Sach::factory()->create(['so_luong' => 2]);

        $response = $this->actingAs($admin)->post(route('admin.phieu-muons.store'), [
            'user_id' => $docGia->id,
            'sach_id' => $sach->id,
            'ngay_muon' => '2026-09-29',
            'ngay_tra_du_kien' => '2026-10-13',
            'ghi_chu' => 'Sách còn nguyên vẹn.',
        ]);

        $phieuMuon = PhieuMuon::query()->firstOrFail();

        $response->assertRedirect(route('admin.phieu-muons.show', $phieuMuon));
        $this->assertDatabaseHas('phieu_muons', [
            'user_id' => $docGia->id,
            'sach_id' => $sach->id,
            'trang_thai' => 'dang_muon',
        ]);
    }

    public function test_admin_cannot_create_loan_when_all_copies_are_borrowed(): void
    {
        $admin = User::factory()->admin()->create();
        $firstReader = User::factory()->docGia()->create();
        $secondReader = User::factory()->docGia()->create();
        $sach = Sach::factory()->create(['so_luong' => 1]);
        PhieuMuon::factory()->for($firstReader)->for($sach)->create();

        $response = $this->actingAs($admin)->post(route('admin.phieu-muons.store'), [
            'user_id' => $secondReader->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->format('Y-m-d'),
            'ngay_tra_du_kien' => today()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('sach_id');
        $this->assertDatabaseCount('phieu_muons', 1);
    }

    public function test_due_date_before_loan_date_returns_validation_error(): void
    {
        $admin = User::factory()->admin()->create();
        $docGia = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.phieu-muons.store'), [
            'user_id' => $docGia->id,
            'sach_id' => $sach->id,
            'ngay_muon' => '2026-09-29',
            'ngay_tra_du_kien' => '2026-09-28',
        ]);

        $response->assertSessionHasErrors('ngay_tra_du_kien');
        $this->assertDatabaseCount('phieu_muons', 0);
    }

    public function test_admin_marks_active_loan_as_returned(): void
    {
        $this->travelTo('2026-09-29 09:00:00');
        $admin = User::factory()->admin()->create();
        $phieuMuon = PhieuMuon::factory()->create([
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.phieu-muons.tra-sach', $phieuMuon));

        $response->assertRedirect();
        $this->assertDatabaseHas('phieu_muons', [
            'id' => $phieuMuon->id,
            'trang_thai' => 'da_tra',
        ]);
        $this->assertSame('2026-09-29', $phieuMuon->refresh()->ngay_tra_thuc_te->toDateString());
    }

    public function test_admin_can_search_loan_by_id(): void
    {
        $admin = User::factory()->admin()->create();
        $phieuMuon1 = PhieuMuon::factory()->create();
        $phieuMuon2 = PhieuMuon::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.phieu-muons.index', ['q' => '#'.$phieuMuon1->id]));

        $response->assertOk()
            ->assertSeeText('#'.$phieuMuon1->id)
            ->assertDontSeeText('#'.$phieuMuon2->id);
    }

    public function test_admin_loan_list_renders_book_covers_in_thumbnail_frames(): void
    {
        $admin = User::factory()->admin()->create();
        PhieuMuon::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.phieu-muons.index'));

        $response->assertOk()
            ->assertSee('class="book-cover-thumbnail rounded overflow-hidden border"', false);
    }

    public function test_admin_exports_filtered_loans_as_excel_compatible_csv(): void
    {
        $this->travelTo('2026-09-30 09:00:00');
        $admin = User::factory()->admin()->create();
        $matchingReader = User::factory()->docGia()->create([
            'name' => '=Nguyễn Văn An',
            'email' => 'an@example.com',
        ]);
        $otherReader = User::factory()->docGia()->create(['name' => 'Trần Bình']);
        $matchingLoan = PhieuMuon::factory()->for($matchingReader)->create([
            'ghi_chu' => '+Cần theo dõi',
        ]);
        PhieuMuon::factory()->for($otherReader)->create();

        $response = $this->actingAs($admin)->get(route('admin.phieu-muons.export', [
            'q' => 'an@example.com',
            'trang_thai' => 'dang_muon',
        ]));

        $response->assertDownload('bao-cao-phieu-muon-2026-09-30.csv');
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('#'.$matchingLoan->id, $content);
        $this->assertStringContainsString("'=Nguyễn Văn An", $content);
        $this->assertStringContainsString("'+Cần theo dõi", $content);
        $this->assertStringNotContainsString('Trần Bình', $content);
    }

    public function test_user_cannot_export_admin_loan_report(): void
    {
        $user = User::factory()->docGia()->create();

        $response = $this->actingAs($user)->get(route('admin.phieu-muons.export'));

        $response->assertForbidden();
    }
}
