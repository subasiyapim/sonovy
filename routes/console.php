<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use App\Models\System\Tenant;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;


    if (Schema::hasTable('tenants') && Tenant::count() > 0) {
        Schedule::job(new QuartersIncomeJob())->daily();
        Schedule::job(new EarningJob())->daily();
        Schedule::job(new IsrcJob())->everyTenMinutes();
        Schedule::job(new MonthlyIncomeJob())->daily();
    }


