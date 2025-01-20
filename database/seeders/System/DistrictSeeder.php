<?php

namespace Database\Seeders\System;

use App\Models\System\City;
use App\Models\System\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $file = File::get(public_path('assets/districts.json'));
        $items = json_decode($file, true);

        // Türkiye şehirlerinin ID'lerini al
        $turkishCities = City::with('country')
            ->whereHas('country', function ($query) {
                $query->where('iso2', 'TR');
            })
            ->get()
            ->keyBy('city_code');

        if ($turkishCities->isNotEmpty()) {
            // Sadece Türkiye ilçelerini filtrele
            $turkishDistricts = collect($items)->where('country_code', 'TR');

            // Bulk insert için veriyi hazırla
            $districts = $turkishDistricts->map(function ($district) use ($turkishCities) {
                $cityId = $turkishCities->get($district['state_code'])?->id;
                
                if ($cityId) {
                    return [
                        'name' => $district['name'],
                        'city_id' => $cityId,
                        'longitude' => $district['longitude'],
                        'latitude' => $district['latitude'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                return null;
            })->filter()->values()->toArray();

            // Bulk insert
            foreach (array_chunk($districts, 100) as $chunk) {
                District::insert($chunk);
            }
        }
    }
}
