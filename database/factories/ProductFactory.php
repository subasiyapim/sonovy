<?php

namespace Database\Factories;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Genre;
use App\Models\Label;
use App\Models\System\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([1, 2, 3]),
            'album_name' => $this->faker->words(3, true),
            'version' => $this->faker->name,
            'mixed_album' => $this->faker->boolean(),
            'genre_id' => Genre::inRandomOrder()->first()->id,
            'sub_genre_id' => Genre::inRandomOrder()->first()->id,
            'format_id' => $this->faker->randomElement([1, 2, 3]),
            'label_id' => Label::inRandomOrder()->first()->id,
            'p_line' => $this->faker->word,
            'c_line' => $this->faker->word,
            'upc_code' => $this->faker->randomNumber(1),
            'catalog_number' => $this->faker->randomNumber(8),
            'language_id' => Country::inRandomOrder()->whereNotNull('language')->first()->id,
            'main_price' => $this->faker->randomFloat(2, 0, 100),
            'created_by' => User::inRandomOrder()->first()->id,
            'production_year' => $this->faker->year,
            'previously_released' => $this->faker->boolean(),
            'previous_release_date' => $this->faker->date(),
            'status' => $this->faker->randomElement([1, 2, 3]),
            'publishing_country_type' => $this->faker->randomElement([1, 2, 3]),
            'note' => $this->faker->text,
            'video_type' => $this->faker->numberBetween(1, 3),
            'description' => $this->faker->text,
            'is_for_kids' => $this->faker->boolean(),
            'grid_code' => $this->faker->word,
            'physical_release_date' => $this->faker->date()
        ];


    }
}
