<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public int $expiryMinutes = 5
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your Password Reset OTP')
            ->view('emails.password-reset-otp');
    }
}

