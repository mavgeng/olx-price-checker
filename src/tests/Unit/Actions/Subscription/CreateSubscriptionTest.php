<?php

namespace Tests\Unit\Actions\Subscription;

use App\Actions\Subscription\CreateSubscription;
use App\Jobs\SendSubscriberVerificationEmail;
use App\Models\Subscriber;
use App\Services\Olx\AdvertUrlParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function action(?string $externalId): CreateSubscription
    {
        $urlParser = $this->createMock(AdvertUrlParser::class);
        $urlParser->method('normalizeUrl')->willReturn('https://www.olx.ua/d/uk/obyavlenie/advert.html');
        $urlParser->method('extractExternalId')->willReturn($externalId);

        return new CreateSubscription($urlParser);
    }

    public function testExecuteCreatesSubscriptionAndDispatchesVerificationEmailForNewSubscriber(): void
    {
        Bus::fake();

        $result = $this->action('925527815')->execute('https://www.olx.ua/d/uk/obyavlenie/advert.html', 'new@example.com');

        $this->assertTrue($result->needVerification);
        $this->assertDatabaseHas('adverts', ['external_id' => '925527815']);
        $this->assertDatabaseHas('advert_subscriptions', ['id' => $result->advertSubscriptionId]);

        Bus::assertDispatched(
            SendSubscriberVerificationEmail::class,
            fn (SendSubscriberVerificationEmail $job) => $job->subscriber->email === 'new@example.com'
        );
    }

    public function testExecuteDoesNotDispatchVerificationEmailForVerifiedSubscriber(): void
    {
        Bus::fake();

        $subscriber = Subscriber::factory()->create(['email' => 'verified@example.com']);

        $result = $this->action('925527815')->execute('https://www.olx.ua/d/uk/obyavlenie/advert.html', $subscriber->email);

        $this->assertFalse($result->needVerification);

        Bus::assertNotDispatched(SendSubscriberVerificationEmail::class);
    }

    public function testExecuteThrowsValidationExceptionWhenExternalIdCannotBeExtracted(): void
    {
        $this->expectException(ValidationException::class);

        $this->action(null)->execute('https://www.olx.ua/d/uk/obyavlenie/advert.html', 'new@example.com');
    }
}