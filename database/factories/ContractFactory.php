<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = $this->faker->date();
        $end_date = Carbon::parse($start_date)->addMonths(rand(1, 10))->format('Y-m-d');
        return [
            'representative_share_ratio' => $this->faker->randomFloat(2, 0, 100),
            'physical_share_ratio' => $this->faker->randomFloat(2, 0, 100),
            'digital_share_ratio' => $this->faker->randomFloat(2, 0, 100),
            'contract_start_date' => $start_date,
            'contract_end_date' => $end_date,
            'auto_renewal' => $this->faker->boolean(),
            'scope' => $this->faker->randomElement([1, 2]),

        ];
    }
}
