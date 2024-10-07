<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    \Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\InitializeTenantMiddleware::class,
])->group(function () {
    
    require __DIR__.'/control.php';

});
