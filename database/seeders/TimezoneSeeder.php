<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_tr = File::get(public_path('assets/timezones.json'));
        $timezones = json_decode($file_tr, true);

        foreach ($timezones as $key => $timezone) {
            Timezone::firstOrCreate(
                ['name' => $timezone['name']],
                ['zone' => $timezone['zone'], 'gmt' => $timezone['gmt'], 'name' => $timezone['name']]
            );
        }
    }
}
