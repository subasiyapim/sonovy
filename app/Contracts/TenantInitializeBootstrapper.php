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

        // This is where you can put your tenant-specific bootstrapping code.

        $disks = config('filesystems.disks');

        \App\Models\System\Tenant::chunk(100, function ($tenants) use (&$disks) {
            foreach ($tenants as $tenant) {
                $disks['tenant_'.$tenant->id] = [
                    'driver' => 'local',
                    'root' => storage_path('app/public/tenant_'.$tenant->id),
                    'url' => env('APP_URL').'/storage/tenant_'.$tenant->id,
                    'visibility' => 'public',
                ];
            }
        });

        config(['filesystems.disks' => $disks]);
    }

    public function revert()
    {
        // ...
    }
}
