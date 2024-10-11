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
        //    \Database\Seeders\System\CountrySeeder::class,
        //    \Database\Seeders\System\CitySeeder::class,
        //    \Database\Seeders\System\DistrictSeeder::class,

            // TENANCY SEEDERS

            \Database\Seeders\PermissionSeeder::class,
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\PermissionRoleSeeder::class,
            \Database\Seeders\RoleUserSeeder::class,

            \Database\Seeders\ContractSeeder::class,
            \Database\Seeders\FeatureSeeder::class,
            \Database\Seeders\GenreSeeder::class,
            \Database\Seeders\IntegrationSeeder::class,
            \Database\Seeders\LabelSeeder::class,
            \Database\Seeders\ArtistBranchSeeder::class,
            \Database\Seeders\ArtistSeeder::class,
            \Database\Seeders\MailTemplateSeeder::class,
            \Database\Seeders\PerformingRightsOrganizationSeeder::class,
            \Database\Seeders\PlanItemSeeder::class,
            \Database\Seeders\PlanSeeder::class,
            \Database\Seeders\PlatformSeeder::class,
            \Database\Seeders\SendNotificationSeeder::class,
            \Database\Seeders\SettingSeeder::class,


        ]);
    }
}
