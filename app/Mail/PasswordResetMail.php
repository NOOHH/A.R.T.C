<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $userEmail;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetUrl, $userEmail, $userName = null)
    {
        $this->resetUrl = $resetUrl;
        $this->userEmail = $userEmail;
        $this->userName = $userName ?: 'User';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('A.R.T.C - Password Reset Request')
            ->view('emails.password_reset')
            ->with([
                'resetUrl' => $this->resetUrl,
                'userEmail' => $this->userEmail,
                'userName' => $this->userName
            ]);
    }
} 
