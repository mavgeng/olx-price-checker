<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendSubscriberVerificationEmail;
use App\Mail\SubscriberVerificationMail;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendSubscriberVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleSendsVerificationEmailToSubscriber(): void
    {
        Mail::fake();

        $subscriber = Subscriber::factory()->unverified()->create();

        new SendSubscriberVerificationEmail($subscriber)->handle();

        Mail::assertSent(
            SubscriberVerificationMail::class,
            fn (SubscriberVerificationMail $mail) => $mail->hasTo($subscriber->email)
                && $mail->subscriber->is($subscriber)
        );
    }
}
