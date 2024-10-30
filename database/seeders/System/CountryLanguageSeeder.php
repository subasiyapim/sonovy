<?php

namespace Database\Seeders\System;

use App\Models\System\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountryLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = File::get(public_path('assets/languages.json'));

        $items = json_decode($file, true);


        foreach ($items as $row) {
            $country = Country::where('iso2', strtoupper($row['code']))->first();
            if ($country) {
                $country->language = $row['name'] ?? null;
                $country->save();
            }
        }

    }
}
