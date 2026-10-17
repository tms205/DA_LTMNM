<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\PhieuMuon;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PhieuMuonControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reader_history_only_displays_own_loans(): void
    {
        $reader = User::factory()->docGia()->create();
        $otherReader = User::factory()->docGia()->create();
        $ownLoan = PhieuMuon::factory()->for($reader)->create();
        $otherLoan = PhieuMuon::factory()->for($otherReader)->create();

        $response = $this->actingAs($reader)->get(route('user.phieu-muons.index'));

        $response->assertOk()
            ->assertSeeText('#'.$ownLoan->id)
            ->assertDontSeeText('#'.$otherLoan->id);
    }

    public function test_reader_can_view_own_loan(): void
    {
        $reader = User::factory()->docGia()->create();
        $phieuMuon = PhieuMuon::factory()->for($reader)->create();

        $response = $this->actingAs($reader)->get(route('user.phieu-muons.show', $phieuMuon));

        $response->assertOk()->assertSeeText($phieuMuon->sach->ten_sach);
    }

    public function test_reader_receives_not_found_for_another_readers_loan(): void
    {
        $reader = User::factory()->docGia()->create();
        $otherReader = User::factory()->docGia()->create();
        $phieuMuon = PhieuMuon::factory()->for($otherReader)->create();

        $response = $this->actingAs($reader)->get(route('user.phieu-muons.show', $phieuMuon));

        $response->assertNotFound();
    }

    public function test_reader_can_search_own_loans_by_book_title(): void
    {
        $reader = User::factory()->docGia()->create();
        $loan1 = PhieuMuon::factory()->for($reader)->create();
        $loan2 = PhieuMuon::factory()->for($reader)->create();

        $response = $this->actingAs($reader)->get(route('user.phieu-muons.index', ['q' => $loan1->sach->ten_sach]));

        $response->assertOk()
            ->assertSeeText('#'.$loan1->id)
            ->assertDontSeeText('#'.$loan2->id);
    }
}
