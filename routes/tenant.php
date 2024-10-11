<?php

declare(strict_types=1);

use App\Http\Middleware\InitializeTenantMiddleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    PreventAccessFromCentralDomains::class,
    InitializeTenancyByDomainOrSubdomain::class,
    InitializeTenantMiddleware::class,
])->group(function () {

    require __DIR__.'/control.php';

});
