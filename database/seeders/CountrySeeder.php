<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $country_file = File::get(public_path('assets/countries.json'));
        $countries = json_decode($country_file, true);

        foreach ($countries as $country) {
            Country::firstOrCreate(
                [
                    'name' => $country['name'],
                    'iso2' => $country['iso2'],
                ],
                [
                    'iso3' => $country['iso3'],
                    'numeric_code' => $country['numeric_code'],
                    'phone_code' => $country['phone_code'],
                    'capital' => $country['capital'],
                    'currency' => $country['currency'],
                    'currency_name' => $country['currency_name'],
                    'currency_symbol' => $country['currency_symbol'],
                    'tld' => $country['tld'],
                    'native' => $country['native'],
                    'region' => $country['region'],
                    'region_id' => $country['region_id'],
                    'subregion' => $country['subregion'],
                    'subregion_id' => $country['subregion_id'],
                    'nationality' => $country['nationality'],
                    'timezones' => $country['timezones'] ?? [],
                    'translations' => $country['translations'] ?? [],
                    'latitude' => $country['latitude'],
                    'longitude' => $country['longitude'],
                    'emoji' => $country['emoji'],
                    'emojiU' => $country['emojiU'],
                    'is_active' => 1,
                ]);
        }
    }
}
