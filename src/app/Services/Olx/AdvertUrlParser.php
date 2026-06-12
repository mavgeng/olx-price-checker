<?php

namespace App\Services\Olx;

use Illuminate\Support\Facades\Http;

class AdvertUrlParser
{
    public function extractExternalId(string $url): ?string
    {
        $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);

        if ($response->failed()) {
            return null;
        }

        if (preg_match('/ad-id=(\d{6,})/', $response->body(), $matchedId)) {
            return $matchedId[1];
        }

        return null;
    }

    public function normalizeUrl(string $url): ?string
    {
        $urlParts = parse_url($url);

        if (
            ! isset($urlParts['scheme'])
            || ! isset($urlParts['host'])
            || ! isset($urlParts['path'])
        ) {
            return null;
        }

        return $urlParts['scheme'].'://'.$urlParts['host'].$urlParts['path'];
    }
}
