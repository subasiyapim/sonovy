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
            'version' => $this->faker->optional()->sentence(2),
            'mixed_album' => $this->faker->boolean(),
            'genre_id' => (new Genre())->newQuery()->inRandomOrder()->first()?->id,
            'sub_genre_id' => (new Genre())->newQuery()->inRandomOrder()->first()?->id,
            'format_id' => $this->faker->randomElement([1, 2, 3]),
            'label_id' => (new Label())->newQuery()->inRandomOrder()->first()?->id,
            'p_line' => '℗ ' . $this->faker->year . ' ' . $this->faker->company,
            'c_line' => '© ' . $this->faker->year . ' ' . $this->faker->company,
            'upc_code' => $this->faker->numerify('#############'),
           'catalog_number' => strtoupper($this->faker->bothify('??###??##')),
            'language_id' => (new Country())->newQuery()->inRandomOrder()->whereNotNull('language')->first()?->id,
            'main_price' => $this->faker->randomFloat(2, 0, 100),
            'created_by' => (new User())->newQuery()->inRandomOrder()->first()?->id,
            'production_year' => $this->faker->year,
            'previously_released' => $this->faker->boolean(),
            'previous_release_date' => $this->faker->date(),
            'status' => $this->faker->randomElement([1, 2, 3]),
            'publishing_country_type' => $this->faker->randomElement([1, 2, 3]),
            'note' => $this->faker->text,
            'video_type' => $this->faker->numberBetween(1, 3),
            'description' => $this->faker->text,
            'is_for_kids' => $this->faker->boolean(),
          'grid_code' => $this->faker->bothify('A1-??##-##-#####'),
            'physical_release_date' => $this->faker->date()
        ];
    }
}
