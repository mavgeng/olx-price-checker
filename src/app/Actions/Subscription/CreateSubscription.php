<?php

namespace App\Actions\Subscription;

use App\Actions\Subscription\Dto\CreateSubscriptionResult;
use App\Jobs\SendSubscriberVerificationEmail;
use App\Models\Advert;
use App\Models\AdvertSubscription;
use App\Models\Subscriber;
use App\Services\Olx\AdvertUrlParser;
use Illuminate\Validation\ValidationException;

class CreateSubscription
{
    public function __construct(
        private readonly AdvertUrlParser $urlParser,
    ) {}

    public function execute(string $url, string $email): CreateSubscriptionResult
    {
        $normalizedUrl = $this->urlParser->normalizeUrl($url);
        $externalId = $normalizedUrl ? $this->urlParser->extractExternalId($normalizedUrl) : null;

        if (! $normalizedUrl || ! $externalId) {
            throw ValidationException::withMessages([
                'url' => 'Could not determine advert. Make sure it is a valid advert.',
            ]);
        }

        $advert = Advert::firstOrCreate(
            ['external_id' => $externalId],
            ['url' => $normalizedUrl]
        );

        $subscriber = Subscriber::firstOrCreate(['email' => $email]);

        $advertSubscription = AdvertSubscription::firstOrCreate([
            'subscriber_id' => $subscriber->id,
            'advert_id' => $advert->id,
        ]);

        $needVerification = $subscriber->email_verified_at === null;
        if ($needVerification) {
            SendSubscriberVerificationEmail::dispatch($subscriber)->onQueue('notifications');
        }

        return new CreateSubscriptionResult(
            advertSubscriptionId: $advertSubscription->id,
            needVerification: $needVerification,
        );
    }
}
