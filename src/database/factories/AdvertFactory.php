<?php

namespace Database\Factories;

use App\Models\Advert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Advert>
 */
class AdvertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => (string) fake()->unique()->numberBetween(100000000, 999999999),
            'url' => fake()->url(),
            'title' => fake()->sentence(3),
            'last_price' => null,
            'currency' => 'UAH',
            'is_active' => true,
            'last_checked_at' => null,
        ];
    }
}
