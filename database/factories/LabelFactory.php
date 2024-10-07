<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'country_id' => \App\Models\System\Country::inRandomOrder()->first()->id,
            'address' => $this->faker->address,
            'added_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
