<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\ISRCServices;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IsrcJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenants;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->tenants = Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

        foreach ($this->tenants as $tenant) {

            tenancy()->initialize($tenant);

            Log::info('Tenant initialized: '.$tenant->domain);


            $songs = Song::whereNull('isrc')->OrWhere('isrc', 0)->get();

            if ($songs->count() == 0) {
                Log::info('No songs found for '.$tenant->domain);
                tenancy()->end();
                Log::info('Tenant ended: '.$tenant->domain);
                continue;
            }

            foreach ($songs as $song) {
                $song->isrc = ISRCServices::make($song->type, $tenant);
                $song->save();
            }

            Log::info('ISRC codes generated for '.$tenant->domain);
            tenancy()->end();
            Log::info('Tenant ended: '.$tenant->domain);
        }

        Cache::forget('tenants');
    }

}
