<?php

namespace Database\Seeders\System;

use App\Models\System\City;
use App\Models\System\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '10G');

        $file = File::get(public_path('assets/districts.json'));

        $items = json_decode($file, true);

        foreach ($items as $row) {

            $city_id = City::with('country')
                ->where('city_code', $row['state_code'])
                ->whereHas('country', function ($query) use ($row) {
                    $query->where('iso2', $row['country_code']);
                })
                ->first()?->id;

            if ($city_id) {
                District::firstOrCreate(
                    [
                        'name' => $row['name'],
                        'city_id' => $city_id,
                    ],
                    [
                        'longitude' => $row['longitude'],
                        'latitude' => $row['latitude'],
                    ],
                );
            }
        }


    }
}
