<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;
use App\Jobs\QuartersIncomeJob;
use Illuminate\Support\Facades\Schedule;

if (\App\Models\System\Tenant::count() > 0) {
    Schedule::job(new QuartersIncomeJob())->dailyAt('00:01');
    Schedule::job(new EarningJob())->everyTenMinutes();
    Schedule::job(new IsrcJob())->everyTenMinutes();
}

