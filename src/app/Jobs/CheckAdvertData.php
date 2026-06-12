<?php

namespace App\Jobs;

use App\Models\Advert;
use App\Services\Olx\AdvertFetcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckAdvertData implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public Advert $advert,
    ) {}

    public function handle(AdvertFetcher $fetcher): void
    {
        $data = $fetcher->fetch($this->advert->external_id);

        if (! $data) {
            return;
        }

        if (! $data->isActive) {
            $this->advert->update([
                'is_active' => false,
                'last_checked_at' => now(),
            ]);

            return;
        }

        $oldPrice = $this->advert->last_price;
        $oldCurrency = $this->advert->currency;

        if ($oldPrice === null) {
            $this->advert->update([
                'is_active' => true,
                'last_price' => $data->price,
                'title' => $data->title,
                'currency' => $data->currency,
                'last_checked_at' => now(),
            ]);

            return;
        }

        if ($oldPrice === $data->price && $oldCurrency === $data->currency) {
            $this->advert->update([
                'is_active' => true,
                'last_checked_at' => now(),
            ]);

            return;
        }

        $this->advert->update([
            'is_active' => true,
            'last_price' => $data->price,
            'currency' => $data->currency,
            'last_checked_at' => now(),
        ]);

        NotifyPriceChange::dispatch($this->advert, $oldPrice, $data->price);
    }
}
