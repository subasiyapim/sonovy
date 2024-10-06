<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //SYSTEM SEEDERS
//            \Database\Seeders\System\CountrySeeder::class,
//            \Database\Seeders\System\CitySeeder::class,
//            \Database\Seeders\System\DistrictSeeder::class,

//            \Database\Seeders\System\TenantSeeder::class,


            //TENANCY SEEDERS
            \Database\Seeders\UserSeeder::class,
        ]);
    }
}
