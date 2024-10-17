<?php

namespace Database\Seeders\System;

use App\Models\System\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $db_name = 'tenant_'.$domain.'_'.$uniq_id;
            $db_user = 'tenant_'.$domain.'_'.$uniq_id;
            $db_password = uniqid();

//            // Veritabanını oluştur
//            DB::statement("CREATE DATABASE $db_name");
//
//            // Sadece localhost için kullanıcı oluştur
//            DB::statement("CREATE USER '$db_user'@'localhost' IDENTIFIED BY '$db_password'");
//
//            // Kullanıcıya localhost için tüm yetkileri ver
//            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost'");

            // Eğer uzak bağlantıya izin vermek istiyorsanız, şu satırları aktif hale getirin:
            // DB::statement("CREATE USER '$db_user'@'%' IDENTIFIED BY '$db_password'");
            // DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'%'");

            // Yetkileri uygula
            // DB::statement("FLUSH PRIVILEGES");

            // Tenant kaydını oluştur
            $tenant = Tenant::create([
                'id' => (string) $uniq_id,
                'name' => $domain,
                'tenancy_db_name' => $db_name,
                'tenancy_db_username' => $db_user,
                'tenancy_db_password' => $db_password,
            ]);

            Log::info('Tenant created: '.json_encode($tenant));

            // Domaini tenant ile ilişkilendir
            $tenant->domains()->create(['domain' => $domain]);

            Log::info('Domain created: '.json_encode($tenant->domains));
        }
    }

}
