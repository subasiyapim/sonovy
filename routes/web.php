<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\SupportController;
use App\Http\Controllers\Front\ContactUsController;
use App\Services\CountryServices;
use App\Services\HelpCenterArticleServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

Route::group(
    [
        'middleware' => [
            // \App\Http\Middleware\HandleInertiaRequests::class,
        ]
    ], function () {


    Route::get('new-tenant', function (\Illuminate\Http\Request $request) {
        $name = $request->get('name');
        $uniq_id = uniqid();
        $db_user = 'tenant_'.$name.'_'.$uniq_id;
        $db_password = uniqid();
        $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $request->name)->exists();


        if ($domain) {
            return 'Domain already exists';
        }

        // Sadece localhost için kullanıcı oluştur
        DB::statement("CREATE USER '$db_user'@'localhost' IDENTIFIED BY '$db_password'");

        // Kullanıcıya localhost için tüm yetkileri ver
        DB::statement("GRANT ALL PRIVILEGES ON 'tenant_'.$name.'_'.$uniq_id.* TO '$db_user'@'localhost'");

        // Yetkileri uygula
        DB::statement("FLUSH PRIVILEGES");
        
        $tenant = \App\Models\System\Tenant::create(
            [
                'id' => (string) $uniq_id,
                'tenancy_db_name' => 'tenant_'.$name.'_'.$uniq_id,
                'name' => 'tenant_'.$name.'_'.$uniq_id,
                'tenancy_db_username' => $db_user,
                'tenancy_db_password' => $db_password,
            ]
        );

        $tenant->domains()->create(['domain' => $name]);

        return $name.'.'.env('BASE_URL').' created';
    });

});