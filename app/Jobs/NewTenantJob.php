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
        $baseDbName = 'tenant_'.$this->domain;
        $dbName = $this->getUniqueDatabaseName($baseDbName);

        $baseUsername = 'tenant_'.$this->domain;
        $username = $this->getUniqueUsername($baseUsername);

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

        // Production environment database password
        if (config('app.env') == 'production') {
            $data['tenancy_db_username'] = $username;
            $data['tenancy_db_password'] = Str::random(16);
        }

        // Create the tenant and associate the domain
        $tenant = \App\Models\System\Tenant::create($data);
        $tenant->domains()->create(['domain' => $this->domain]);

        $tenantKey = $tenant->getTenantKey();

        Log::info("Tenant key: {$tenantKey}");
        Log::info("Tenant created successfully with domain: {$this->domain}");
    }

    /**
     * Generate a unique database name by appending an incrementing number if needed.
     *
     * @param  string  $baseDbName
     * @return string
     */
    private function getUniqueDatabaseName(string $baseDbName): string
    {
        $dbName = $baseDbName;
        $counter = 1;

        while ($this->databaseExists($dbName)) {
            $dbName = $baseDbName.'_'.$counter;
            $counter++;
        }

        return $dbName;
    }

    /**
     * Generate a unique username by appending an incrementing number if needed.
     *
     * @param  string  $baseUsername
     * @return string
     */
    private function getUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        while ($this->usernameExists($username)) {
            $username = $baseUsername.'_'.$counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Check if a database with the given name already exists.
     *
     * @param  string  $dbName
     * @return bool
     */
    private function databaseExists(string $dbName): bool
    {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
        $result = \DB::select($query, [$dbName]);

        return !empty($result);
    }

    /**
     * Check if a username already exists.
     *
     * @param  string  $username
     * @return bool
     */
    private function usernameExists(string $username): bool
    {
        $query = "SELECT User FROM mysql.user WHERE User = ?";
        $result = \DB::select($query, [$username]);

        return !empty($result);
    }
}
