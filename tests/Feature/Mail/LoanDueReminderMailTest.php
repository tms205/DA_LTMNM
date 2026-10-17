<?php

namespace Tests\Feature\Mail;

use App\Mail\LoanDueReminderMail;
use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LoanDueReminderMailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_email_renders_grouped_due_loans_and_escapes_reader_data(): void
    {
        $reader = User::factory()->make([
            'name' => 'Nguyễn An <script>alert(1)</script>',
            'email' => 'reader@example.com',
        ]);
        $dueTodayBook = Sach::factory()->make(['ten_sach' => 'Lập trình PHP']);
        $dueTomorrowBook = Sach::factory()->make(['ten_sach' => 'Laravel thực chiến']);
        $dueTodayLoan = PhieuMuon::factory()->make(['ngay_tra_du_kien' => '2026-09-30']);
        $dueTodayLoan->id = 10;
        $dueTodayLoan->setRelation('sach', $dueTodayBook);
        $dueTomorrowLoan = PhieuMuon::factory()->make(['ngay_tra_du_kien' => '2026-10-01']);
        $dueTomorrowLoan->id = 11;
        $dueTomorrowLoan->setRelation('sach', $dueTomorrowBook);

        $mail = new LoanDueReminderMail(
            $reader,
            new Collection([$dueTodayLoan]),
            new Collection([$dueTomorrowLoan]),
        );
        $html = $mail->render();

        $this->assertStringContainsString('Lập trình PHP', $html);
        $this->assertStringContainsString('Laravel thực chiến', $html);
        $this->assertStringContainsString('Đến hạn hôm nay', $html);
        $this->assertStringContainsString('Sắp đến hạn ngày mai', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }
}
