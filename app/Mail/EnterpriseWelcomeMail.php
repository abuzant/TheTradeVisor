<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EnterpriseAdmin;
use App\Models\EnterpriseBroker;

class EnterpriseWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $token;
    public $broker;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(EnterpriseAdmin $admin, string $token, EnterpriseBroker $broker)
    {
        $this->admin = $admin;
        $this->token = $token;
        $this->broker = $broker;
        $this->resetUrl = url('enterprise-password-reset/' . $token . '?email=' . urlencode($admin->email));
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to TheTradeVisor Enterprise Portal')
                    ->view('emails.enterprise-welcome');
    }
}
