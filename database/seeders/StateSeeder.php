<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $country_file = File::get(public_path('assets/countries.json'));
        $countries = json_decode($country_file, true);

        $states_file = File::get(public_path('assets/states.json'));
        $states = json_decode($states_file, true);

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        DB::table('cities')->truncate();
        DB::table('states')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($states as $state) {
            // Find the corresponding country_id for each state
            $country_id = Country::where('iso2', $state['country_code'])->first()?->id;

            if ($country_id) {
                State::firstOrCreate(
                    [
                        'name' => $state['name'],
                    ],
                    [
                        'code' => $state['state_code'],
                        'country_id' => $country_id,
                        'country_code' => $state['country_code'],
                    ],
                );
            }
        }
    }
}
