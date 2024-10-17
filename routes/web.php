<?php

use Carbon\Carbon;
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


    Route::get('new-tenant', function (Request $request) {
        $domainName = $request->get('name');
        $uniqId = uniqid();
        $dbName = 'tenant_'.$domainName.'_'.Carbon::now()->format('dmHis');
        $dbUser = 'tenant_'.$domainName;
        $dbPassword = uniqid();

        // Check if the domain already exists
        $domainExists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domainName)->exists();

        if ($domainExists) {
            return response()->json(['message' => 'Domain already exists'], 400);
        }

        // Create tenant
        $tenant = \App\Models\System\Tenant::create([
            'id' => $uniqId,
            'tenancy_db_name' => $dbName,
            'name' => 'tenant_'.$domainName,
            'tenancy_db_username' => $dbUser,
            'tenancy_db_password' => $dbPassword,
        ]);

        // Associate domain with tenant
        $tenant->domains()->create(['domain' => $domainName]);

        return response()->json([
            'message' => 'Tenant created successfully',
            'url' => $domainName.'.'.env('BASE_URL')
        ], 201);
    });

});