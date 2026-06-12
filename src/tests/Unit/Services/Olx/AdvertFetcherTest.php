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

    public function test_assert_api_url_not_null(): void
    {
        $this->assertNotNull(config('olx.offers_api_url'));
    }

    public function test_fetch_returns_dto_for_active_advert(): void
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

    public function test_fetch_returns_inactive_dto_when_status_is_not_active(): void
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

    public function test_fetch_returns_null_when_response_is_not_successful(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response(null, 404),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNull($dto);
    }

    public function test_fetch_returns_null_when_data_is_missing(): void
    {
        Http::fake([
            $this->url('925527815') => Http::response(['data' => null]),
        ]);

        $dto = new AdvertFetcher(app(Factory::class))->fetch('925527815');

        $this->assertNull($dto);
    }

    public function test_fetch_returns_null_when_price_param_is_missing(): void
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
