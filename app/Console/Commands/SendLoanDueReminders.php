<?php

namespace App\Console\Commands;

use App\Services\LoanDueReminderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('loans:send-due-reminders')]
#[Description('Gửi email nhắc hạn trả cho độc giả có sách đến hạn hôm nay hoặc ngày mai')]
class SendLoanDueReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(LoanDueReminderService $reminders): int
    {
        $result = $reminders->sendToReaders($reminders->eligibleUserIds());

        $this->info("Đã gửi {$result['sent']} email nhắc hạn; bỏ qua {$result['skipped']} độc giả đã nhận hôm nay.");

        return self::SUCCESS;
    }
}
