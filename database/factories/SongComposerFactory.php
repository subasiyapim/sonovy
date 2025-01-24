<?php

namespace Database\Factories;

use App\Models\SongComposer;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongComposerFactory extends Factory
{
    protected $model = SongComposer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 