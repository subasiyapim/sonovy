<?php

namespace Database\Seeders\System;

use App\Models\System\City;
use App\Models\System\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = File::get(public_path('assets/cities.json'));

        $items = json_decode($file, true);

        foreach ($items as $row) {
            $country_id = Country::where('iso2', $row['country_code'])->first()?->id;

            if ($country_id) {
                City::firstOrCreate(
                    [
                        'name' => $row['name'],
                        'city_code' => $row['state_code'],
                    ],
                    [
                        'country_id' => $country_id,
                        'longitude' => $row['longitude'],
                        'latitude' => $row['latitude'],
                    ],
                );
            }
        }
    }
}
