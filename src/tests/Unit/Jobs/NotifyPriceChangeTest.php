<?php

namespace Tests\Unit\Jobs;

use App\Jobs\NotifyPriceChange;
use App\Mail\AdvertPriceChangedMail;
use App\Models\Advert;
use App\Models\AdvertSubscription;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotifyPriceChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_sends_email_to_verified_subscribers_only(): void
    {
        Mail::fake();

        $advert = Advert::factory()->create();

        $verified = Subscriber::factory()->create();
        $unverified = Subscriber::factory()->unverified()->create();

        AdvertSubscription::create(['advert_id' => $advert->id, 'subscriber_id' => $verified->id]);
        AdvertSubscription::create(['advert_id' => $advert->id, 'subscriber_id' => $unverified->id]);

        new NotifyPriceChange($advert, 100000, 90000)->handle();

        Mail::assertSent(
            AdvertPriceChangedMail::class,
            fn (AdvertPriceChangedMail $mail) => $mail->hasTo($verified->email)
                && $mail->advert->is($advert)
                && $mail->oldPrice === 100000
                && $mail->newPrice === 90000
        );

        Mail::assertNotSent(
            AdvertPriceChangedMail::class,
            fn (AdvertPriceChangedMail $mail) => $mail->hasTo($unverified->email)
        );
    }

    public function test_handle_sends_nothing_when_there_are_no_verified_subscribers(): void
    {
        Mail::fake();

        $advert = Advert::factory()->create();

        $unverified = Subscriber::factory()->unverified()->create();
        AdvertSubscription::create(['advert_id' => $advert->id, 'subscriber_id' => $unverified->id]);

        new NotifyPriceChange($advert, 100000, 90000)->handle();

        Mail::assertNothingSent();
    }
}
