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
        $validate = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'domain' => 'required|string|unique:tenants,name'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 422);
        }


        \App\Jobs\NewTenantJob::dispatch($request->domain);
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