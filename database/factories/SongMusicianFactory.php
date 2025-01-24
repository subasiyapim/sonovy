<?php

namespace Database\Factories;

use App\Models\SongMusician;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongMusicianFactory extends Factory
{
    protected $model = SongMusician::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'instrument' => fake()->randomElement(['Gitar', 'Piyano', 'Davul', 'Bas', 'Keman']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 