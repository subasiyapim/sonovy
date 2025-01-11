<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;
use App\Jobs\QuartersIncomeJob;
use Illuminate\Support\Facades\Schedule;

if (\App\Models\System\Tenant::count() > 0) {
    Schedule::job(new QuartersIncomeJob())->everyMinute();
    Schedule::job(new EarningJob())->everyTenMinutes();
    Schedule::job(new IsrcJob())->everyTenMinutes();
}

