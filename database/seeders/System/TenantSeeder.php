<?php

namespace Database\Seeders\System;

use App\Models\System\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        foreach (['app'] as $domain) {

            $uniq_id = uniqid();
            $db_name = 'tenant_'.$domain.'_'.Carbon::now()->format('d_m_Y_H_i_s');
            $db_user = 'tenant_'.$domain.'_'.$uniq_id;
            $db_password = uniqid();

//            // Veritabanını oluştur
            //DB::statement("CREATE DATABASE $db_name");


            //      Eğer uzak bağlantıya izin vermek istiyorsanız, şu satırları aktif hale getirin:
//            DB::statement("CREATE USER '$db_user'@'%' IDENTIFIED BY '$db_password'");
//            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost'");


            // Tenant kaydını oluştur
            $tenant = Tenant::create([
                'id' => (string) $uniq_id,
                'name' => $domain,
                'tenancy_db_name' => $db_name,
                'tenancy_db_username' => $db_user,
                'tenancy_db_password' => $db_password,
            ]);

            // Domaini tenant ile ilişkilendir
            $tenant->domains()->create(['domain' => $domain]);

            // Sadece localhost için kullanıcı oluştur
            DB::statement("CREATE USER '$db_user'@'localhost' IDENTIFIED BY '$db_password'");

            // Kullanıcıya localhost için tüm yetkileri ver
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost'");

            // Yetkileri uygula
            DB::statement("FLUSH PRIVILEGES");
        }
    }

}
