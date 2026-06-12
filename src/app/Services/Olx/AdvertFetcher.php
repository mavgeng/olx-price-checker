<?php

namespace App\Services\Olx;

use App\Services\Dto\FetchAdvertDataDto;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Arr;

class AdvertFetcher
{
    private const string BASE_URL = 'https://www.olx.ua/api/v1/offers/';

    public function __construct(
        private HttpClient $http,
    ) {}

    public function fetch(string $externalId): ?FetchAdvertDataDto
    {
        $response = $this->http
            ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->timeout(5)
            ->get(self::BASE_URL.$externalId.'/');

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            return null;
        }

        $price = $this->extractPrice($data['params'] ?? []);

        if ($price === null) {
            return null;
        }

        return new FetchAdvertDataDto(
            price: $price['amount'],
            currency: $price['currency'],
            title: $data['title'] ?? '',
            isActive: $this->isActive($data),
        );
    }

    private function extractPrice(array $params): ?array
    {
        $priceParam = collect($params)->firstWhere('key', 'price');

        if (! $priceParam) {
            return null;
        }

        $value = Arr::get($priceParam, 'value.value');
        $currency = Arr::get($priceParam, 'value.currency', 'UAH');

        if (! is_numeric($value)) {
            return null;
        }

        return [
            'amount' => (int) round(((float) $value) * 100),
            'currency' => $currency,
        ];
    }

    private function isActive(array $data): bool
    {
        $validTo = $data['valid_to_time'] ?? null;

        return $validTo !== null && strtotime($validTo) > time();
    }
}
