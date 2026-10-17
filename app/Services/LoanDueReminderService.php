<?php

namespace App\Services;

use App\Mail\LoanDueReminderMail;
use App\Models\PhieuMuon;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LoanDueReminderService
{
    /**
     * @return array<int, int>
     */
    public function eligibleUserIds(): array
    {
        return PhieuMuon::query()
            ->where('trang_thai', '!=', 'da_tra')
            ->where(function ($query): void {
                $query->whereDate('ngay_tra_du_kien', today())
                    ->orWhereDate('ngay_tra_du_kien', today()->addDay());
            })
            ->distinct()
            ->orderBy('user_id')
            ->pluck('user_id')
            ->map(fn (int $userId): int => $userId)
            ->all();
    }

    /**
     * @return Collection<int, array{user: User, loans: Collection<int, PhieuMuon>, sent_today: bool}>
     */
    public function readersDueOn(Carbon $date): Collection
    {
        return PhieuMuon::query()
            ->with(['user', 'sach.theLoai'])
            ->where('trang_thai', '!=', 'da_tra')
            ->whereDate('ngay_tra_du_kien', $date)
            ->orderBy('user_id')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $loans): array {
                $user = $loans->firstOrFail()->user;

                return [
                    'user' => $user,
                    'loans' => $loans,
                    'sent_today' => $this->wasSentToday($user->id),
                ];
            })
            ->values();
    }

    /**
     * @param  array<int, int>  $userIds
     * @return array{sent: int, skipped: int}
     */
    public function sendToReaders(array $userIds, bool $force = false): array
    {
        $loansByReader = PhieuMuon::query()
            ->with(['user', 'sach.theLoai'])
            ->whereIn('user_id', $userIds)
            ->where('trang_thai', '!=', 'da_tra')
            ->where(function ($query): void {
                $query->whereDate('ngay_tra_du_kien', today())
                    ->orWhereDate('ngay_tra_du_kien', today()->addDay());
            })
            ->orderBy('user_id')
            ->orderBy('ngay_tra_du_kien')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

        $sent = 0;
        $skipped = 0;

        foreach ($loansByReader as $userId => $loans) {
            $cacheKey = $this->sentCacheKey((int) $userId);

            if (! $force && ! Cache::add($cacheKey, 'sending', now()->addDays(2))) {
                $skipped++;

                continue;
            }

            try {
                $reader = $loans->firstOrFail()->user;
                $dueTodayLoans = $loans->filter(
                    fn (PhieuMuon $loan): bool => $loan->ngay_tra_du_kien->isToday()
                )->values();
                $dueTomorrowLoans = $loans->filter(
                    fn (PhieuMuon $loan): bool => $loan->ngay_tra_du_kien->isTomorrow()
                )->values();

                Mail::to($reader)->send(new LoanDueReminderMail(
                    $reader,
                    $dueTodayLoans,
                    $dueTomorrowLoans,
                ));
                Cache::put($cacheKey, 'sent', now()->addDays(2));
                $sent++;
            } catch (Throwable $exception) {
                if (! $force) {
                    Cache::forget($cacheKey);
                }

                throw $exception;
            }
        }

        return compact('sent', 'skipped');
    }

    public function wasSentToday(int $userId): bool
    {
        return Cache::has($this->sentCacheKey($userId));
    }

    private function sentCacheKey(int $userId): string
    {
        return 'loan-due-reminder:'.today()->toDateString().":user:{$userId}";
    }
}
