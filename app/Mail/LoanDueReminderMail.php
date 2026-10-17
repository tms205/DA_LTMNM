<?php

namespace App\Mail;

use App\Models\PhieuMuon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LoanDueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, PhieuMuon>  $dueTodayLoans
     * @param  Collection<int, PhieuMuon>  $dueTomorrowLoans
     */
    public function __construct(
        public User $reader,
        public Collection $dueTodayLoans,
        public Collection $dueTomorrowLoans,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nhắc hạn trả sách - Thư viện HUIT',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.due-reminder',
            with: [
                'reader' => $this->reader,
                'dueTodayLoans' => $this->dueTodayLoans,
                'dueTomorrowLoans' => $this->dueTomorrowLoans,
                'loanHistoryUrl' => route('user.phieu-muons.index'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
