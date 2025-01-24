<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\System\Country;
use App\Models\User;
use App\Enums\SongTypeEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Song;

class SongFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'version' => $this->faker->word,
            'genre_id' => (new Genre())->newQuery()->inRandomOrder()->first()?->id,
            'sub_genre_id' => (new Genre())->newQuery()->inRandomOrder()->first()?->id,
            'type' => ProductTypeEnum::cases()[array_rand(ProductTypeEnum::cases())],
            'isrc' => strtoupper($this->faker->lexify('??'). $this->faker->numerify('###')),
            'is_instrumental' => $this->faker->boolean,
            'language_id' => (new Country())->newQuery()->inRandomOrder()->whereNotNull('language')->first()?->id,
            'lyrics' => $this->faker->paragraphs(3, true),
            'iswc' => $this->faker->numerify('T-#######-#'),
            'preview_start' => ['start' => $this->faker->numberBetween(0, 30)],
            'released_before' => $this->faker->boolean,
            'original_release_date' => $this->faker->date(),
            'created_by' => (new User())->newQuery()->inRandomOrder()->first()?->id,
            'status' => 1,
            'duration' => $this->faker->numberBetween(60, 300),
            'is_completed' => true,
        ];
    }
} 