<?php

namespace App\Services;

class AdvertUrlParser
{
    public function extractExternalId(string $url): ?string
    {
        if (preg_match('/-ID([A-Za-z0-9]+)\.html/i', $url, $matchedId)) {
            return $matchedId[1];
        }

        return null;
    }

    public function normalizeUrl(string $url): ?string
    {
        $urlParts = parse_url($url);

        if (
            !isset($urlParts['scheme'])
            || !isset($urlParts['host'])
            || !isset($urlParts['path'])
        ) {
            return null;
        }

        return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];
    }
}
