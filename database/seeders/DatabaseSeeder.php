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
            // \Database\Seeders\System\CountrySeeder::class,

            // TENANCY SEEDERS
            \Database\Seeders\PermissionSeeder::class,
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\PermissionRoleSeeder::class,
            \Database\Seeders\RoleUserSeeder::class,
            \Database\Seeders\PlatformSeeder::class,
            \Database\Seeders\ContractSeeder::class,
            \Database\Seeders\FeatureSeeder::class,
            \Database\Seeders\GenreSeeder::class,
            \Database\Seeders\IntegrationSeeder::class,
            \Database\Seeders\LabelSeeder::class,
            \Database\Seeders\ArtistBranchSeeder::class,
            \Database\Seeders\ArtistSeeder::class,
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\ArtistProductSeeder::class,
        ]);
    }
}
