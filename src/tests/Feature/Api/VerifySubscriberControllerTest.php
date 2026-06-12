<?php

namespace Tests\Feature\Api;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifySubscriberControllerTest extends TestCase
{
    use RefreshDatabase;

    private function verifyUrl(Subscriber $subscriber, ?string $hash = null): string
    {
        return URL::temporarySignedRoute(
            'subscribers.verify',
            now()->addHours(24),
            [
                'subscriber' => $subscriber->id,
                'hash' => $hash ?? sha1($subscriber->email),
            ]
        );
    }

    public function test_verifies_subscriber_with_valid_signature(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get($this->verifyUrl($subscriber));

        $response->assertOk();
        $response->assertJson(['message' => 'Email verified.']);

        $this->assertNotNull($subscriber->refresh()->email_verified_at);
    }

    public function test_rejects_invalid_hash(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get($this->verifyUrl($subscriber, hash: 'invalid-hash'));

        $response->assertForbidden();
        $this->assertNull($subscriber->refresh()->email_verified_at);
    }

    public function test_rejects_unsigned_request(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get("/api/subscribers/{$subscriber->id}/verify/".sha1($subscriber->email));

        $response->assertForbidden();
        $this->assertNull($subscriber->refresh()->email_verified_at);
    }
}
