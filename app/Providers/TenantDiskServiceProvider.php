<?php

namespace App\Providers;

use App\Models\System\Tenant;
use Illuminate\Support\ServiceProvider;

class TenantDiskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $disks = config('filesystems.disks');

        Tenant::chunk(100, function ($tenants) use (&$disks) {
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
}
