<?php

namespace Database\Seeders\System;

use App\Models\System\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = File::get(public_path('assets/countries.json'));
        $items = json_decode($file, true);

        // Sadece Türkiye'yi ekle
        $turkeyData = collect($items)->where('iso2', 'TR')->first();

        if ($turkeyData) {
            Country::firstOrCreate(
                [
                    'name' => $turkeyData['name'],
                    'iso2' => $turkeyData['iso2'],
                ],
                [
                    'iso3' => $turkeyData['iso3'],
                    'numeric_code' => $turkeyData['numeric_code'],
                    'phone_code' => $turkeyData['phone_code'],
                    'capital' => $turkeyData['capital'],
                    'currency' => $turkeyData['currency'],
                    'currency_name' => $turkeyData['currency_name'],
                    'currency_symbol' => $turkeyData['currency_symbol'],
                    'tld' => $turkeyData['tld'],
                    'native' => $turkeyData['native'],
                    'region' => $turkeyData['region'],
                    'subregion' => $turkeyData['subregion'],
                    'nationality' => $turkeyData['nationality'],
                    'timezones' => $turkeyData['timezones'] ?? [],
                    'translations' => $turkeyData['translations'] ?? [],
                    'latitude' => $turkeyData['latitude'],
                    'longitude' => $turkeyData['longitude'],
                    'emoji' => $turkeyData['emoji'],
                    'emojiU' => $turkeyData['emojiU'],
                ]);
        }

        $this->call([
            CountrySeeder::class,
            CountryLanguageSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
        ]);
    }
}
