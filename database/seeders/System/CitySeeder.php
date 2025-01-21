<?php

namespace Database\Seeders\System;

use App\Models\System\City;
use App\Models\System\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = File::get(public_path('assets/cities.json'));
        $items = json_decode($file, true);

        // Türkiye'nin ID'sini al
        $turkeyId = Country::where('iso2', 'TR')->first()?->id;

        if ($turkeyId) {
            // Sadece Türkiye şehirlerini filtrele
            $turkishCities = collect($items)->where('country_code', 'TR');

            // Bulk insert için veriyi hazırla
            $cities = $turkishCities->map(function ($city) use ($turkeyId) {
                return [
                    'name' => $city['name'],
                    'city_code' => $city['state_code'],
                    'country_id' => $turkeyId,
                    'longitude' => $city['longitude'],
                    'latitude' => $city['latitude'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            // Bulk insert
            foreach (array_chunk($cities, 100) as $chunk) {
                City::insert($chunk);
            }
        }
    }
}
