<?php

namespace App\Console\Commands;

use App\Events\Test\Ws;
use App\Models\System\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class wstest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant) {
            Log::debug(tenant()->id);
            Ws::dispatch('test');
        });
    }
}
