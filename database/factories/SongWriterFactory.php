<?php

namespace Database\Factories;

use App\Models\SongWriter;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongWriterFactory extends Factory
{
    protected $model = SongWriter::class;

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