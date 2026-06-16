<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public ?string $email,
        public string $phone,
        public string $title,
        public string $messageBody,
    ) {
    }

    public function build(): self
    {
        $mail = $this->subject('New Contact Enquiry: ' . $this->title)
            ->view('emails.contact-form');

        if ($this->email) {
            $mail->replyTo($this->email, $this->name);
        }

        return $mail;
    }
}
