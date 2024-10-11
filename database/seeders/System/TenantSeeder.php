<?php

namespace Database\Seeders\System;

use App\Models\System\Tenant;
use Illuminate\Database\Seeder;

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

        Tenant::get()->each(function ($tenant) {
            $tenant->domains()->delete();
            $tenant->delete();
        });

        foreach (['app', 'demo'] as $domain) {
            $uniq_id = uniqid();

            $tenant = Tenant::create([
                'id' => (string) $uniq_id,
                'name' => $domain,
                'tenancy_db_name' => 'tenant_'.$domain.'_'.$uniq_id,
            ]);

            $tenant->domains()->create(['domain' => $domain]);

        }
    }

}
