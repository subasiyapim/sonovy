<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'country_id' => \App\Models\System\Country::inRandomOrder()->first()->id,
            'ipi_code' => $this->faker->randomNumber(8),
            'isni_code' => $this->faker->randomNumber(8),
            'phone' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'about' => $this->faker->text,
            'created_by' => 1,
        ];
    }
}
