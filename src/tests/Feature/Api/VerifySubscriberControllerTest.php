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

    public function testVerifiesSubscriberWithValidSignature(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get($this->verifyUrl($subscriber));

        $response->assertOk();
        $response->assertJson(['message' => 'Email verified.']);

        $this->assertNotNull($subscriber->refresh()->email_verified_at);
    }

    public function testRejectsInvalidHash(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get($this->verifyUrl($subscriber, hash: 'invalid-hash'));

        $response->assertForbidden();
        $this->assertNull($subscriber->refresh()->email_verified_at);
    }

    public function testRejectsUnsignedRequest(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create();

        $response = $this->get("/api/subscribers/{$subscriber->id}/verify/".sha1($subscriber->email));

        $response->assertForbidden();
        $this->assertNull($subscriber->refresh()->email_verified_at);
    }
}
