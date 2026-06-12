<?php

namespace App\Services\Dto;

class FetchAdvertDataDto
{
    public function __construct(
        public int $price,
        public string $currency,
        public string $title,
        public bool $isActive,
    ) {}
}
