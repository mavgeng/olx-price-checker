<?php

namespace App\Actions\Subscription\Dto;

class CreateSubscriptionResult
{
    public function __construct(
        public readonly string $advertSubscriptionId,
        public readonly bool $needVerification,
    ) {
    }
}