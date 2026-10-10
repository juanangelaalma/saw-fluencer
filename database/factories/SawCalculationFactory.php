<?php

namespace Database\Factories;

use App\Models\SawCalculation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SawCalculation>
 */
class SawCalculationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_influencers' => fake()->numberBetween(1, 10),
            'payload' => [
                'criteria' => [
                    ['code' => 'C1', 'name' => 'Engagement Rate', 'weight' => 100, 'type' => 'benefit', 'type_label' => 'Benefit', 'divisor' => 5],
                ],
                'rows' => [
                    ['rank' => 1, 'name' => 'Influencer A', 'username' => 'influencer_a', 'score' => 1.0, 'criteria_scores' => [['raw' => '5.00', 'likert' => 5, 'normalized' => 1.0, 'weighted' => 1.0]]],
                ],
            ],
        ];
    }
}
