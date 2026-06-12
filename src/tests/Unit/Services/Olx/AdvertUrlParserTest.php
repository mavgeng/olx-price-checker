<?php

namespace Tests\Unit\Services\Olx;

use App\Services\Olx\AdvertUrlParser;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdvertUrlParserTest extends TestCase
{
    public function testNormalizeUrl(): void
    {
        $parser = new AdvertUrlParser;

        $url = 'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html?reason=hp%7Cpromoted';

        $this->assertSame(
            'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html',
            $parser->normalizeUrl($url)
        );
    }

    public function testNormalizeUrlReturnsNullWhenPathIsMissing(): void
    {
        $parser = new AdvertUrlParser;

        $this->assertNull($parser->normalizeUrl('https://www.olx.ua'));
    }

    public function testExtractExternalIdReturnsIdFromPageBody(): void
    {
        $url = 'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html';

        Http::fake([
            $url => Http::response('<script>window.config = {"adId": "x", "params": "ad-id=925527815&other=1"};</script>'),
        ]);

        $parser = new AdvertUrlParser;

        $this->assertSame('925527815', $parser->extractExternalId($url));
    }

    public function testExtractExternalIdReturnsNullWhenPatternNotFound(): void
    {
        $url = 'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html';

        Http::fake([
            $url => Http::response('<div>no ad id here</div>'),
        ]);

        $parser = new AdvertUrlParser;

        $this->assertNull($parser->extractExternalId($url));
    }

    public function testExtractExternalIdReturnsNullWhenRequestFails(): void
    {
        $url = 'https://www.olx.ua/d/uk/obyavlenie/nov-krosvki-nike-sb-dunk-rozmri-41-45-ID10Dq3Z.html';

        Http::fake([
            $url => Http::response('Server Error', 500),
        ]);

        $parser = new AdvertUrlParser;

        $this->assertNull($parser->extractExternalId($url));
    }
}
