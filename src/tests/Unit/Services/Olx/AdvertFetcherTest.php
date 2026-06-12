<?php

namespace Tests\Unit\Services\Olx;

use App\Services\Olx\AdvertFetcher;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdvertFetcherTest extends TestCase
{
    private function url(string $externalId): string
    {
        return config('olx.offers_api_url').$externalId.'/';
    }

    public function testAssertApiUrlNotNull(): void
    {
        $this->assertNotNull(config('olx.offers_api_url'));
    }

    public function testFetchReturnsDtoForActiveAdvert(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response([
                'data' => [
                    'title' => 'Nike SB Dunk',
                    'status' => 'active',
                    'params' => [
                        ['key' => 'price', 'value' => ['value' => 1234.5, 'currency' => 'UAH']],
                    ],
                ],
            ]),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNotNull($dto);
        $this->assertSame(123450, $dto->price);
        $this->assertSame('UAH', $dto->currency);
        $this->assertSame('Nike SB Dunk', $dto->title);
        $this->assertTrue($dto->isActive);
    }

    public function testFetchReturnsInactiveDtoWhenStatusIsNotActive(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response([
                'data' => [
                    'title' => 'Nike SB Dunk',
                    'status' => 'inactive',
                    'params' => [
                        ['key' => 'price', 'value' => ['value' => 1234.5, 'currency' => 'UAH']],
                    ],
                ],
            ]),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNotNull($dto);
        $this->assertFalse($dto->isActive);
    }

    public function testFetchReturnsNullWhenResponseIsNotSuccessful(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response(null, 404),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNull($dto);
    }

    public function testFetchReturnsNullWhenDataIsMissing(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response(['data' => null]),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNull($dto);
    }

    public function testFetchReturnsNullWhenPriceParamIsMissing(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response([
                'data' => [
                    'title' => 'Nike SB Dunk',
                    'status' => 'active',
                    'params' => [
                        ['key' => 'color', 'value' => ['value' => 'black']],
                    ],
                ],
            ]),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNull($dto);
    }
}
