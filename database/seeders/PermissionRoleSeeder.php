<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);

        //Admin Role
        Role::where('code', 'admin')->first()->permissions()->syncWithoutDetaching(
            Permission::whereNotIn('code',
                [

                ]
            )->get(['id']));

        //User Role
        Role::where('code', 'user')->first()->permissions()->syncWithoutDetaching(
            Permission::whereIn('code',
                [
                    'user_list',
                    'user_create',
                    'user_edit',
                    'user_delete',
                    'user_show',
                    'artist_list',
                    'artist_create',
                    'artist_edit',
                    'artist_delete',
                    'artist_show',
                    'copyright_list',
                    'copyright_demand',
                    'copyright_create',
                    'label_list',
                    'label_create',
                    'label_edit',
                    'label_show',
                    'product_list',
                    'product_create',
                    'product_edit',
                    'product_delete',
                    'product_show',
                    'song_list',
                    'song_create',
                    'song_edit',
                    'song_delete',
                    'song_show',
                    'author_list',
                    'author_create',
                    'author_edit',
                    'author_delete',
                    'author_show',
                    'contract_list',
                    'contract_create',
                    'contract_edit',
                    'contract_delete',
                    'contract_show',
                    'work_list',
                    'work_create',
                    'work_edit',
                    'work_delete',
                    'work_show',
                    'finance_earning_list',
                    'report_list',
                    'report_create',
                    'report_edit',
                    'report_delete',
                    'report_show',
                    'payment_list',
                ])->get(['id']));

    }
}
