<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;

class VerifySubscriberController
{
    public function __invoke(Subscriber $subscriber, string $hash): JsonResponse
    {
        if (! hash_equals($hash, sha1($subscriber->email))) {
            abort(403);
        }

        if ($subscriber->email_verified_at === null) {
            $subscriber->update(['email_verified_at' => now()]);
        }

        return response()->json(['message' => 'Email verified.']);
    }
}
