<?php

namespace App\Jobs;

use App\Mail\SubscriberVerificationMail;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSubscriberVerificationEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscriber $subscriber,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->subscriber->email)->send(new SubscriberVerificationMail($this->subscriber));
    }
}
