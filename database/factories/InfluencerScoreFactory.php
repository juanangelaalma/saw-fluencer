<?php

namespace Database\Factories;

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\InfluencerScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InfluencerScore>
 */
class InfluencerScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'influencer_id' => Influencer::factory(),
            'criterion_id' => Criterion::factory(),
            'raw_value' => fake()->randomFloat(2, 1, 1000),
            'likert_value' => fake()->numberBetween(1, 5),
        ];
    }
}
