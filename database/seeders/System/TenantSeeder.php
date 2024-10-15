<?php

namespace Database\Seeders\System;

use App\Models\System\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Tenant verileri.
     */

    /**
     * Run the database seeds.
     */
    public function run()
    {

//        Tenant::get()->each(function ($tenant) {
//            if (DB::connection('system')->getSchemaBuilder()->hasTable($tenant->tenancy_db_name)) {
//                DB::statement("DROP DATABASE ".$tenant->tenancy_db_name);
//                $tenant->domains()->delete();
//                $tenant->delete();
//            }
//        });


        foreach (['app', 'demo'] as $domain) {

            $uniq_id = uniqid();
            $db_name = 'tenant_'.$domain.'_'.$uniq_id;
            $db_user = 'tenant_'.$domain.'_'.$uniq_id;
            $db_password = uniqid();

            DB::statement("CREATE DATABASE $db_name");
            DB::statement("CREATE USER '$db_user'@'localhost' IDENTIFIED BY '$db_password'");
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'%'");
            DB::statement("FLUSH PRIVILEGES");

            $tenant = Tenant::create([
                'id' => (string) $uniq_id,
                'name' => $domain,
                'tenancy_db_name' => $db_name,
                'tenancy_db_username' => $db_user,
                'tenancy_db_password' => $db_password,
            ]);

            $tenant->domains()->create(['domain' => $domain]);

        }
    }

}
