<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LoanDueReminderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoanReminderController extends Controller
{
    public function index(LoanDueReminderService $reminders): View
    {
        $dueToday = $reminders->readersDueOn(today());
        $dueTomorrow = $reminders->readersDueOn(today()->addDay());

        return view('admin.loan-reminders.index', compact('dueToday', 'dueTomorrow'));
    }

    public function store(Request $request, LoanDueReminderService $reminders): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'user'),
            ],
            'force' => ['sometimes', 'boolean'],
        ]);

        $result = $reminders->sendToReaders(
            array_values(array_unique(array_map('intval', $validated['user_ids']))),
            $request->boolean('force'),
        );

        if ($result['sent'] === 0) {
            return to_route('admin.loan-reminders.index')
                ->with('info', 'Các độc giả được chọn đã nhận thông báo hôm nay hoặc không còn phiếu cần nhắc.');
        }

        $message = "Đã gửi email nhắc hạn thành công cho {$result['sent']} độc giả.";
        if ($result['skipped'] > 0) {
            $message .= " Bỏ qua {$result['skipped']} độc giả đã nhận thông báo hôm nay.";
        }

        return to_route('admin.loan-reminders.index')->with('success', $message);
    }
}
