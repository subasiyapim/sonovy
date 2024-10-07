<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permission_role')->truncate();
        DB::table('permissions')->truncate();
        DB::table('permission_translations')->truncate();
        DB::table('role_user')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_translations')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PermissionRoleSeeder::class,
            RoleUserSeeder::class
        ]);
    }
}
