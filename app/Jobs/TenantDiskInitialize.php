<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TenantDiskInitialize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    public $tenant;

    /**
     * Yeni bir job instance oluşturun.
     *
     * @param  \App\Models\System\Tenant  $tenant
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Job'u çalıştır.
     */
    public function handle(): void
    {
        $diskName = 'tenant_'.$this->tenant->id;
        $diskPath = storage_path('app/public/'.$diskName);

        // Disk dizinini oluştur
        if (!file_exists($diskPath)) {
            mkdir($diskPath, 0755, true);
        }

        // Disk yapılandırmasını dinamik olarak oluştur
        Storage::build([
            'driver' => 'local',
            'root' => $diskPath,
            'url' => env('APP_URL').'/storage/'.$diskName,
            'visibility' => 'public',
        ]);

        Log::info("Tenant için disk başarıyla oluşturuldu: ".$diskName);
    }
}