<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CheckAdvertData;
use App\Jobs\NotifyPriceChange;
use App\Models\Advert;
use App\Services\Dto\FetchAdvertDataDto;
use App\Services\Olx\AdvertFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CheckAdvertDataTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleMarksAdvertInactiveWhenFetcherReturnsNull(): void
    {
        $advert = Advert::factory()->create([
            'is_active' => true,
            'last_checked_at' => null,
        ]);

        $fetcher = $this->createMock(AdvertFetcher::class);
        $fetcher->method('fetch')->willReturn(null);

        new CheckAdvertData($advert)->handle($fetcher);

        $advert->refresh();
        $this->assertFalse($advert->is_active);
        $this->assertNotNull($advert->last_checked_at);
    }

    public function testHandleMarksAdvertInactiveWhenDataIsNotActive(): void
    {
        $advert = Advert::factory()->create([
            'is_active' => true,
            'last_price' => 10000,
        ]);

        $fetcher = $this->createMock(AdvertFetcher::class);
        $fetcher->method('fetch')->willReturn(new FetchAdvertDataDto(
            price: 10000,
            currency: 'UAH',
            title: 'Nike SB Dunk',
            isActive: false,
        ));

        new CheckAdvertData($advert)->handle($fetcher);

        $advert->refresh();
        $this->assertFalse($advert->is_active);
        $this->assertSame(10000, $advert->last_price);
    }

    public function testHandleSetsInitialPriceWhenAdvertHasNoPriceYet(): void
    {
        Bus::fake();

        $advert = Advert::factory()->create([
            'last_price' => null,
            'title' => null,
        ]);

        $fetcher = $this->createMock(AdvertFetcher::class);
        $fetcher->method('fetch')->willReturn(new FetchAdvertDataDto(
            price: 123450,
            currency: 'UAH',
            title: 'Nike SB Dunk',
            isActive: true,
        ));

        new CheckAdvertData($advert)->handle($fetcher);

        $advert->refresh();
        $this->assertTrue($advert->is_active);
        $this->assertSame(123450, $advert->last_price);
        $this->assertSame('Nike SB Dunk', $advert->title);
        $this->assertSame('UAH', $advert->currency);

        Bus::assertNotDispatched(NotifyPriceChange::class);
    }

    public function testHandleUpdatesLastCheckedAtWhenPriceAndCurrencyUnchanged(): void
    {
        Bus::fake();

        $advert = Advert::factory()->create([
            'last_price' => 123450,
            'currency' => 'UAH',
        ]);

        $fetcher = $this->createMock(AdvertFetcher::class);
        $fetcher->method('fetch')->willReturn(new FetchAdvertDataDto(
            price: 123450,
            currency: 'UAH',
            title: 'Nike SB Dunk',
            isActive: true,
        ));

        new CheckAdvertData($advert)->handle($fetcher);

        $advert->refresh();
        $this->assertTrue($advert->is_active);
        $this->assertSame(123450, $advert->last_price);
        $this->assertNotNull($advert->last_checked_at);

        Bus::assertNotDispatched(NotifyPriceChange::class);
    }

    public function testHandleDispatchesNotifyPriceChangeWhenPriceChanges(): void
    {
        Bus::fake();

        $advert = Advert::factory()->create([
            'last_price' => 100000,
            'currency' => 'UAH',
        ]);

        $fetcher = $this->createMock(AdvertFetcher::class);
        $fetcher->method('fetch')->willReturn(new FetchAdvertDataDto(
            price: 90000,
            currency: 'UAH',
            title: 'Nike SB Dunk',
            isActive: true,
        ));

        new CheckAdvertData($advert)->handle($fetcher);

        $advert->refresh();
        $this->assertSame(90000, $advert->last_price);

        Bus::assertDispatched(
            NotifyPriceChange::class,
            fn (NotifyPriceChange $job) => $job->advert->is($advert)
                && $job->oldPrice === 100000
                && $job->newPrice === 90000
        );
    }
}
