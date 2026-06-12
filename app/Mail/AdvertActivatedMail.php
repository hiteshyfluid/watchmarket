<?php

namespace App\Mail;

use App\Models\MembershipOrder;
use App\Services\InvoicePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdvertActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipOrder $order)
    {
        $this->order->loadMissing(['user', 'level', 'advert']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Watch Market advert is now live',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.advert-activated',
            with: [
                'order' => $this->order,
                'advert' => $this->order->advert,
                'advertUrl' => $this->order->advert
                    ? route('market.show', $this->order->advert)
                    : route('my-account'),
            ],
        );
    }

    public function attachments(): array
    {
        $invoicePdfService = app(InvoicePdfService::class);

        return [
            Attachment::fromData(
                fn () => $invoicePdfService->render($this->order),
                $invoicePdfService->fileName($this->order)
            )->withMime('application/pdf'),
        ];
    }
}
