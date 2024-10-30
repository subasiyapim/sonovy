<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TenantDiskInitialize implements ShouldQueue
{
    use Queueable;

    public $tenant;

    /**
     * Create a new job instance.
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disks = config('filesystems.disks');

        $disks['tenant_'.$this->tenant->id] = [
            'driver' => 'local',
            'root' => storage_path('app/public/tenant_'.$this->tenant->id),
            'url' => env('APP_URL').'/storage/tenant_'.$this->tenant->id,
            'visibility' => 'public',
        ];

        config(['filesystems.disks' => $disks]);
    }
}
