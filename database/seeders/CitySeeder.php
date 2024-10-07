<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '10G');
        $file_tr = File::get(public_path('assets/cities.json'));
        $cities = json_decode($file_tr, true);


        foreach ($cities as $city) {
            $state_id = State::where('code', $city['state_code'])->where('country_code', $city['country_code'])->first()?->id;

            if ($state_id) {
                City::firstOrCreate(
                    [
                        'name' => $city['name'],
                    ],
                    [
                        'state_id' => $state_id,
                        'name' => $city['name'],
                    ],
                );
            }
        }


    }
}
