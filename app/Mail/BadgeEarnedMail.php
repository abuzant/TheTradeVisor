<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ProfileVerificationBadge;
use App\Models\User;
use App\Models\TradingAccount;

class BadgeEarnedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $badge;
    public $user;
    public $account;
    public $profileUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(ProfileVerificationBadge $badge, User $user, TradingAccount $account, ?string $profileUrl = null)
    {
        $this->badge = $badge;
        $this->user = $user;
        $this->account = $account;
        $this->profileUrl = $profileUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 You\'ve earned a new badge: ' . $this->badge->badge_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.badge-earned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
