<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Broadcast>
 */
class BroadcastFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([1, 2, 3]);

        $data = [
            'type' => $type, // Ensure 'type' is always set
            'added_by' => User::inRandomOrder()->first()->id,
        ];

        if ($type === 1) {
            $data['copyright_for_publication_image'] = $this->faker->name;
            $data['label_id'] = Label::inRandomOrder()->first()->id;
            $data['right_to_perform_work'] = $this->faker->name;
        } elseif ($type === 2) {
            $data = array_merge($data, [
                'has_audiovisual_rights' => $this->faker->boolean,
                'name' => $this->faker->name,
                'publisher_name' => $this->faker->name,
                'is_for_children' => $this->faker->boolean,
                'copyright_owner' => $this->faker->name,
                'youtube_channel' => $this->faker->name,
                'youtube_channel_theme' => $this->faker->name,
            ]);
        }

        $barcode_type = $this->faker->randomElement([1, 2, 3]);
        $data['barcode_type'] = $barcode_type;

        if ($barcode_type === 1) {
            $data['upc_code'] = $this->faker->numberBetween(1000, 999999);
        } elseif ($barcode_type === 2) {
            $data['ean_code'] = $this->faker->numberBetween(1000, 999999);
        } elseif ($barcode_type === 3) {
            $data['jan_code'] = $this->faker->numberBetween(1000, 999999);
        }

        $data['genre_id'] = Genre::inRandomOrder()->first()->id;
        $data['sub_genre_id'] = Genre::inRandomOrder()->first()->id;
        $data['is_compilation_publication'] = $this->faker->boolean;
        $data['catalog_number'] = $this->faker->numberBetween(1000, 999999);
        $data['language_id'] = Country::inRandomOrder()->first()->id;
        $data['remaster_release_date'] = $this->faker->date();
        $data['p_line'] = $this->faker->name;
        return $data;
    }


}
