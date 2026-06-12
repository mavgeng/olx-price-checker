<?php

namespace App\Console\Commands;

use App\Jobs\CheckAdvertData;
use App\Models\Advert;
use Illuminate\Console\Command;

class CheckAdvertsCommand extends Command
{
    protected $signature = 'adverts:check';

    protected $description = 'Dispatch advert check jobs for adverts with verified subscribers';

    public function handle(): void
    {
        Advert::query()
            ->where('is_active', true)
            ->whereHas('subscribers', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->chunkById(200, function ($adverts) {
                foreach ($adverts as $advert) {
                    CheckAdvertData::dispatch($advert)->onQueue('checks');
                }
            });
    }
}
