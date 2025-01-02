<?php

namespace App\Contracts;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Log;

class TenantInitializeBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $publicDisks = [
            'products',
            'songs',
            'artists',
            'labels',
            'earning_reports',
        ];

        foreach ($publicDisks as $publicDisk) {
            config([
                "filesystems.disks.tenant_{$tenant->domain}_{$publicDisk}" => [
                    'driver' => 'local',
                    'root' => storage_path("app/public/tenant_{$tenant->domain}_{$publicDisk}"),
                    'url' => env('APP_URL')."/storage/tenant_{$tenant->domain}_{$publicDisk}",
                    'visibility' => 'public',
                ],
            ]);
        }
    }

    public function revert()
    {
        Log::info('TenantInitializeBootstrapper revert called');

    }
}