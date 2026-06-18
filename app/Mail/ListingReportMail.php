<?php

namespace App\Mail;

use App\Models\ListingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ListingReport $report) {}

    public function build(): self
    {
        $mail = $this->subject('New Listing Report: ' . $this->report->issueLabel())
            ->view('emails.listing-report');

        if ($this->report->reporter_email) {
            $mail->replyTo($this->report->reporter_email, $this->report->reporter_name ?? 'Reporter');
        }

        return $mail;
    }
}
