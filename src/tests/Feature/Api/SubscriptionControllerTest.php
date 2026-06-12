<?php

namespace Tests\Feature\Api;

use App\Models\AdvertSubscription;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const string ADVERT_URL = 'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html';

    public function testStoreCreatesSubscriptionAndRequiresVerificationForNewSubscriber(): void
    {
        Http::fake([
            self::ADVERT_URL => Http::response('ad-id=925527815'),
        ]);

        $response = $this->postJson('/api/subscriptions', [
            'url' => self::ADVERT_URL.'?reason=hp%7Cpromoted',
            'email' => 'new-subscriber@example.com',
        ]);

        $response->assertCreated();
        $response->assertJson([
            'message' => 'You need to verify your email address to finish your subscription.',
        ]);
        $response->assertJsonStructure(['id', 'message']);

        $this->assertDatabaseCount('advert_subscriptions', 1);
    }

    public function testStoreCompletesSubscriptionForAlreadyVerifiedSubscriber(): void
    {
        Http::fake([
            self::ADVERT_URL => Http::response('ad-id=925527815'),
        ]);

        $subscriber = Subscriber::factory()->create(['email' => 'verified@example.com']);

        $response = $this->postJson('/api/subscriptions', [
            'url' => self::ADVERT_URL,
            'email' => $subscriber->email,
        ]);

        $response->assertCreated();
        $response->assertJson([
            'message' => 'Your subscription has been completed.',
        ]);
    }

    public function testStoreReturnsValidationErrorForDisallowedHost(): void
    {
        $response = $this->postJson('/api/subscriptions', [
            'url' => 'https://www.example.com/some/advert',
            'email' => 'subscriber@example.com',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['url']);
    }

    public function testStoreReturnsValidationErrorForMissingFields(): void
    {
        $response = $this->postJson('/api/subscriptions', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['url', 'email']);
    }

    public function testStoreReturnsValidationErrorWhenExternalIdCannotBeExtracted(): void
    {
        Http::fake([
            self::ADVERT_URL => Http::response('no ad id here'),
        ]);

        $response = $this->postJson('/api/subscriptions', [
            'url' => self::ADVERT_URL,
            'email' => 'subscriber@example.com',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['url']);

        $this->assertDatabaseCount('advert_subscriptions', 0);
    }
}