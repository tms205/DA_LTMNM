<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Mail\LoanDueReminderMail;
use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoanReminderControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_sees_readers_grouped_by_due_today_and_due_tomorrow(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $admin = User::factory()->admin()->create();
        $dueTodayReader = User::factory()->docGia()->create(['name' => 'Độc giả đến hạn']);
        $dueTomorrowReader = User::factory()->docGia()->create(['name' => 'Độc giả sắp đến hạn']);
        $ignoredReader = User::factory()->docGia()->create(['name' => 'Độc giả chưa cần nhắc']);
        $dueTodayBook = Sach::factory()->create(['ten_sach' => 'Sách đến hạn hôm nay']);
        $dueTomorrowBook = Sach::factory()->create(['ten_sach' => 'Sách đến hạn ngày mai']);
        $laterBook = Sach::factory()->create(['ten_sach' => 'Sách còn nhiều ngày']);
        $returnedBook = Sach::factory()->create(['ten_sach' => 'Sách đã trả']);

        PhieuMuon::factory()->for($dueTodayReader)->for($dueTodayBook, 'sach')->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        PhieuMuon::factory()->for($dueTomorrowReader)->for($dueTomorrowBook, 'sach')->create([
            'ngay_tra_du_kien' => '2026-10-01',
        ]);
        PhieuMuon::factory()->for($ignoredReader)->for($laterBook, 'sach')->create([
            'ngay_tra_du_kien' => '2026-10-02',
        ]);
        PhieuMuon::factory()->for($dueTodayReader)->for($returnedBook, 'sach')->daTra()->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);

        $response = $this->actingAs($admin)->get('/admin/nhac-han-tra');

        $response->assertOk()
            ->assertSeeText('Sắp đến hạn ngày mai')
            ->assertSeeText('Đến hạn hôm nay')
            ->assertSeeText('Độc giả đến hạn')
            ->assertSeeText('Sách đến hạn hôm nay')
            ->assertSeeText('Độc giả sắp đến hạn')
            ->assertSeeText('Sách đến hạn ngày mai')
            ->assertDontSeeText('Độc giả chưa cần nhắc')
            ->assertDontSeeText('Sách còn nhiều ngày')
            ->assertDontSeeText('Sách đã trả');
    }

    public function test_reader_is_forbidden_from_loan_reminder_page(): void
    {
        $reader = User::factory()->docGia()->create();

        $response = $this->actingAs($reader)->get('/admin/nhac-han-tra');

        $response->assertForbidden();
    }

    public function test_admin_sends_one_aggregated_email_to_selected_reader(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $admin = User::factory()->admin()->create();
        $selectedReader = User::factory()->docGia()->create(['email' => 'selected@example.com']);
        $otherReader = User::factory()->docGia()->create(['email' => 'other@example.com']);
        $dueTodayBook = Sach::factory()->create(['ten_sach' => 'Sách hạn hôm nay']);
        $dueTomorrowBook = Sach::factory()->create(['ten_sach' => 'Sách hạn ngày mai']);

        PhieuMuon::factory()->for($selectedReader)->for($dueTodayBook, 'sach')->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        PhieuMuon::factory()->for($selectedReader)->for($dueTomorrowBook, 'sach')->create([
            'ngay_tra_du_kien' => '2026-10-01',
        ]);
        PhieuMuon::factory()->for($otherReader)->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        Mail::fake();

        $response = $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', [
            'user_ids' => [$selectedReader->id],
        ]);

        $response->assertRedirect('/admin/nhac-han-tra')
            ->assertSessionHas('success');
        Mail::assertSent(LoanDueReminderMail::class, function (LoanDueReminderMail $mail) use ($selectedReader): bool {
            return $mail->hasTo($selectedReader->email)
                && $mail->dueTodayLoans->count() === 1
                && $mail->dueTomorrowLoans->count() === 1;
        });
    }

    public function test_regular_send_skips_reader_already_notified_today(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $admin = User::factory()->admin()->create();
        $reader = User::factory()->docGia()->create();
        PhieuMuon::factory()->for($reader)->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        Mail::fake();

        $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', ['user_ids' => [$reader->id]]);
        $response = $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', ['user_ids' => [$reader->id]]);

        $response->assertRedirect('/admin/nhac-han-tra')
            ->assertSessionHas('info');
        Mail::assertSent(LoanDueReminderMail::class, 1);
    }

    public function test_force_send_resends_notification_to_reader(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $admin = User::factory()->admin()->create();
        $reader = User::factory()->docGia()->create();
        PhieuMuon::factory()->for($reader)->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        Mail::fake();

        $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', ['user_ids' => [$reader->id]]);
        $response = $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', [
            'user_ids' => [$reader->id],
            'force' => true,
        ]);

        $response->assertRedirect('/admin/nhac-han-tra')
            ->assertSessionHas('success');
        Mail::assertSent(LoanDueReminderMail::class, 2);
    }

    public function test_page_marks_reader_already_notified_today_and_offers_resend(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $admin = User::factory()->admin()->create();
        $reader = User::factory()->docGia()->create(['name' => 'Độc giả đã nhận mail']);
        PhieuMuon::factory()->for($reader)->create([
            'ngay_tra_du_kien' => '2026-09-30',
        ]);
        Mail::fake();
        $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', ['user_ids' => [$reader->id]]);

        $response = $this->actingAs($admin)->get('/admin/nhac-han-tra');

        $response->assertOk()
            ->assertSeeText('Đã gửi hôm nay')
            ->assertSeeText('Gửi lại');
    }

    public function test_reader_cannot_send_loan_reminders(): void
    {
        $reader = User::factory()->docGia()->create();
        Mail::fake();

        $response = $this->actingAs($reader)->post('/admin/nhac-han-tra/gui', [
            'user_ids' => [$reader->id],
        ]);

        $response->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_admin_must_select_at_least_one_reader(): void
    {
        $admin = User::factory()->admin()->create();
        Mail::fake();

        $response = $this->actingAs($admin)->post('/admin/nhac-han-tra/gui', [
            'user_ids' => [],
        ]);

        $response->assertSessionHasErrors('user_ids');
        Mail::assertNothingSent();
    }
}
