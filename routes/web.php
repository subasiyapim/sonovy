<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(
    [
        'middleware' => [
            \App\Http\Middleware\InitializeTenantMiddleware::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain::class,
        ]
    ]
    , function () {

    Route::get('/', function () {
        return Inertia::render('Welcome');
    });

    require __DIR__.'/auth.php';
});