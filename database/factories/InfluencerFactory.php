<?php

namespace Database\Factories;

use App\Models\Influencer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Influencer>
 */
class InfluencerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => Str::lower(fake()->unique()->bothify('influencer_##??')),
        ];
    }
}
