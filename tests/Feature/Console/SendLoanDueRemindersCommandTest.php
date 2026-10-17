<?php

namespace Tests\Feature\Console;

use App\Mail\LoanDueReminderMail;
use App\Models\PhieuMuon;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendLoanDueRemindersCommandTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_automatic_due_reminder_command_is_registered(): void
    {
        Artisan::call('list', ['--format' => 'json']);

        $this->assertStringContainsString('loans:send-due-reminders', Artisan::output());
    }

    public function test_command_sends_one_daily_email_per_eligible_reader(): void
    {
        $this->travelTo('2026-09-30 08:00:00');
        $firstReader = User::factory()->docGia()->create();
        $secondReader = User::factory()->docGia()->create();
        $ignoredReader = User::factory()->docGia()->create();
        PhieuMuon::factory()->for($firstReader)->count(2)->sequence(
            ['ngay_tra_du_kien' => '2026-09-30'],
            ['ngay_tra_du_kien' => '2026-10-01'],
        )->create();
        PhieuMuon::factory()->for($secondReader)->create([
            'ngay_tra_du_kien' => '2026-10-01',
        ]);
        PhieuMuon::factory()->for($ignoredReader)->create([
            'ngay_tra_du_kien' => '2026-10-02',
        ]);
        Mail::fake();

        $this->artisan('loans:send-due-reminders')->assertSuccessful();
        $this->artisan('loans:send-due-reminders')->assertSuccessful();

        Mail::assertSent(LoanDueReminderMail::class, 2);
    }

    public function test_command_is_scheduled_to_run_every_morning(): void
    {
        Artisan::call('schedule:list');

        $output = Artisan::output();
        $this->assertStringContainsString('loans:send-due-reminders', $output);
        $this->assertStringContainsString('0 8 * * *', $output);
    }
}
