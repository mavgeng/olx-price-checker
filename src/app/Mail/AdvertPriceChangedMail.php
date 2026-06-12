<?php

namespace App\Mail;

use App\Models\Advert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdvertPriceChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Advert $advert,
        public int $oldPrice,
        public int $newPrice,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Price update for '.$this->advert->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscribers.price_changed',
            with: [
                'advert' => $this->advert,
                'oldPrice' => $this->oldPrice,
                'newPrice' => $this->newPrice,
            ],
        );
    }
}
