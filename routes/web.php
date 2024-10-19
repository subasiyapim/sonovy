<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(
    [
        'middleware' => [
            // \App\Http\Middleware\HandleInertiaRequests::class,
        ]
    ], function () {

    Route::get('new-tenant', function (Request $request) {
        $domainName = $request->get('name');
        $uniqId = uniqid();
        $dbName = 'tenant_'.$domainName.'_'.$uniqId;
        $dbUser = 'tenant_'.$domainName;
        $dbPassword = uniqid();

        // Check if the domain already exists
        $domainExists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domainName)->exists();

        if ($domainExists) {
            return response()->json(['message' => 'Domain already exists'], 400);
        }

        // Create tenant

        $data = [
            'id' => $uniqId,
            'tenancy_db_name' => $dbName,
            'name' => $domainName,
        ];

        if (env('APP_ENV') != 'local') {
            $data['tenancy_db_username'] = $dbUser;
            $data['tenancy_db_password'] = $dbPassword;
        }

        $tenant = \App\Models\System\Tenant::create($data);

        // Associate domain with tenant
        $tenant->domains()->create(['domain' => $domainName]);

        return response()->json([
            'message' => 'Tenant created successfully',
            'url' => $domainName.'.'.env('BASE_URL')
        ], 201);
    });

    Route::get('delete-tenant', function (Request $request) {
        $tenant = \App\Models\System\Tenant::where('name', $request->name)->first();

        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $tenant->delete();

        return response()->json(['message' => 'Tenant deleted successfully'], 200);
    });
});