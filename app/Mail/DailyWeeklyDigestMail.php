<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DailyWeeklyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $digest;
    public string $frequency;

    public function __construct(User $user, array $digest, string $frequency)
    {
        $this->user = $user;
        $this->digest = $digest;
        $this->frequency = $frequency;
    }

    public function build()
    {
        $subject = ucfirst($this->frequency)." trading digest";

        return $this->subject($subject)
            ->view('emails.digest');
    }
}
