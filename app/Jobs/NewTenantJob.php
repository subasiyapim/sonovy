<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Reverb\Loggers\Log;

class NewTenantJob implements ShouldQueue
{
    use Queueable;

    public string $domain;

    /**
     * Create a new job instance.
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $uniqId = uniqid();
        $dbName = 'tenant_'.$this->domain.'_'.$uniqId;

        // Check if the domain already exists
        $domainExists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $this->domain)->exists();

        if ($domainExists) {
            Log::error($this->domain.' domain already exists');
        }

        // Create tenant
        $data = [
            'id' => $uniqId,
            'tenancy_db_name' => $dbName,
            'name' => $this->domain,
        ];

        if (config('app.env') == 'production') {
            $dbUser = 'tenant_'.$this->domain;
            $dbPassword = uniqid();

            $data['tenancy_db_username'] = $dbUser;
            $data['tenancy_db_password'] = $dbPassword;
        }

        $tenant = \App\Models\System\Tenant::create($data);
        $tenant->domains()->create(['domain' => $this->domain]);

        Log::info('Tenant created successfully domain: '.$this->domain);
    }
}
