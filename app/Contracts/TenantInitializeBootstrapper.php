<?php

namespace App\Contracts;

use Illuminate\Support\Facades\Session;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

class TenantInitializeBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        tenancy()->initialize($tenant);
    }

    public function revert()
    {
        // ...
    }
}
