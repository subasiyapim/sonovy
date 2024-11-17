<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $domain;

    /**
     * Create a new job instance.
     *
     * @param  string  $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
        Log::info("Creating tenant with domain: {$domain}");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dbName = 'tenant_'.$this->domain;

        // Check if the domain already exists
        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $this->domain)->exists()) {
            Log::error("Domain '{$this->domain}' already exists");
            return; // Exit to avoid duplicate creation
        }

        // Prepare tenant data
        $data = [
            'tenancy_db_name' => $dbName,
            'domain' => $this->domain,
        ];

        // Production environment database user and password
        if (config('app.env') == 'production') {
            $data['tenancy_db_username'] = 'tenant_'.$this->domain;
            $data['tenancy_db_password'] = Str::random(16);
        }

        // Create the tenant and associate the domain
        $tenant = \App\Models\System\Tenant::create($data);
        $tenant->domains()->create(['domain' => $this->domain]);

        $tenantKey = $tenant->getTenantKey();

        Log::info("Tenant key: {$tenantKey}");
        Log::info("Tenant created successfully with domain: {$this->domain}");
    }
}