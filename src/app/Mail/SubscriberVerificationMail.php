<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SubscriberVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm your subscription',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $verifyUrl = URL::temporarySignedRoute(
            'subscribers.verify',
            now()->addHours(24),
            [
                'subscriber' => $this->subscriber->id,
                'hash' => sha1($this->subscriber->email),
            ]
        );

        return new Content(
            markdown: 'emails.subscribers.verify',
            with: [
                'email' => $this->subscriber->email,
                'verifyUrl' => $verifyUrl,
            ],
        );
    }
}
