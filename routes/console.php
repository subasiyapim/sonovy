<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use App\Models\System\Tenant;
use Illuminate\Support\Facades\Schedule;

if (Tenant::count() > 0) {
    Schedule::job(new QuartersIncomeJob())->everyMinute();
    Schedule::job(new EarningJob())->everyTenMinutes();
    Schedule::job(new IsrcJob())->everyTenMinutes();
    Schedule::job(new MonthlyIncomeJob())->everyMinute();
}

