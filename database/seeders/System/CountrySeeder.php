<?php

namespace Database\Seeders\System;

use App\Models\System\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = File::get(public_path('assets/countries.json'));

        $items = json_decode($file, true);

        foreach ($items as $row) {
            Country::firstOrCreate(
                [
                    'name' => $row['name'],
                    'iso2' => $row['iso2'],
                ],
                [
                    'iso3' => $row['iso3'],
                    'numeric_code' => $row['numeric_code'],
                    'phone_code' => $row['phone_code'],
                    'capital' => $row['capital'],
                    'currency' => $row['currency'],
                    'currency_name' => $row['currency_name'],
                    'currency_symbol' => $row['currency_symbol'],
                    'tld' => $row['tld'],
                    'native' => $row['native'],
                    'region' => $row['region'],
                    'subregion' => $row['subregion'],
                    'nationality' => $row['nationality'],
                    'timezones' => $row['timezones'] ?? [],
                    'translations' => $row['translations'] ?? [],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'emoji' => $row['emoji'],
                    'emojiU' => $row['emojiU'],
                ]);
        }

        $this->call([
            CountryLanguageSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
        ]);
    }
}
