<?php

namespace App\Jobs;

use App\Mail\AdvertPriceChangedMail;
use App\Models\Advert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyPriceChange implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public Advert $advert,
        public int $oldPrice,
        public int $newPrice,
    ) {}

    public function handle(): void
    {
        $subscribers = $this->advert->subscribers()
            ->whereNotNull('email_verified_at')
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(
                new AdvertPriceChangedMail($this->advert, $this->oldPrice, $this->newPrice)
            );
        }
    }
}
