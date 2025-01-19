<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-tenant {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');

        $baseDbName = 'tenant_'.$domain;
        $dbName = $this->getUniqueDatabaseName($baseDbName);

        $baseUsername = 'tenant_'.$domain;
        $username = $this->getUniqueUsername($baseUsername);

        // Check if the domain already exists
        $tenant = \App\Models\System\Tenant::where('domain', $domain)->first();

        if ($tenant) {
            $this->warn("Tenant with domain {$domain} already exists!");
            
            if (!$this->confirm('Do you want to delete existing tenant and create a new one?')) {
                $this->info('Operation cancelled.');
                return;
            }

            $this->info("Proceeding with tenant deletion...");
            
            try {
                // Delete the tenant record first
                $tenant->delete();
                $this->info("Existing tenant deleted successfully.");

                // Then try to drop the database if it exists
                if ($this->databaseExists($tenant->tenancy_db_name)) {
                    $dropQuery = "DROP DATABASE IF EXISTS `{$tenant->tenancy_db_name}`";
                    DB::statement($dropQuery);
                    $this->info("Existing database deleted successfully.");
                }
            } catch (\Exception $e) {
                $this->warn("An error occurred but proceeding with new tenant creation: " . $e->getMessage());
            }
        }

        // Prepare tenant data
        $data = [
            'tenancy_db_name' => $dbName,
            'domain' => $domain,
        ];

        // Production environment database password
        if (config('app.env') == 'production') {
            $data['tenancy_db_username'] = $username;
            $data['tenancy_db_password'] = Str::random(16);
        }

        $this->info("Creating tenant with domain {$domain}...");

        $tenant = \App\Models\System\Tenant::create($data);
        $tenant->domains()->create(['domain' => $domain]);

        $tenantKey = $tenant->getTenantKey();

        $this->info("Tenant key: {$tenantKey}");
        $this->info("Tenant created successfully with domain: {$domain}");

    }

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
     * Check if a database with the given name already exists.
     *
     * @param  string  $dbName
     * @return bool
     */
    private function databaseExists(string $dbName): bool
    {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
        $result = DB::select($query, [$dbName]);

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
