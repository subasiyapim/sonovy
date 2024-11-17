<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(
    [
        'middleware' => [
            'web'
        ]
    ],
    function () {

        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('create-tenant', function (Request $request) {
            $validate = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'domain' => 'required|string|unique:domains,domain'
            ]);

            if ($validate->fails()) {
                return response()->json(['message' => $validate->errors()->first()], 422);
            }


            \App\Jobs\NewTenantJob::dispatch($request->domain);
        });

        Route::get('delete-tenant', function (Request $request) {

            $validate = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'domain' => 'required|string|exists:domains,domain'
            ]);

            $tenant = \App\Models\System\Tenant::where('domain', $request->domain)->first();

            if (!$tenant) {
                return response()->json(['message' => 'Tenant not found'], 404);
            }

            $tenant->delete();

            return response()->json(['message' => 'Tenant deleted successfully'], 200);
        });
    }
);
