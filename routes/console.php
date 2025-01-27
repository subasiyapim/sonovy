<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use App\Models\System\Tenant;
use Illuminate\Support\Facades\Schedule;

$tenants = Tenant::all();

if ($tenants->count() > 0) {
    foreach ($tenants as $tenant) {
        tenancy()->initialize($tenant);

        Schedule::job(new QuartersIncomeJob())->daily();
        Schedule::job(new EarningJob())->everyMinute();
        Schedule::job(new IsrcJob())->everyMinute();
        Schedule::job(new MonthlyIncomeJob())->daily();

        tenancy()->end();
    }
}


