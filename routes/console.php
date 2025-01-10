<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;
use App\Jobs\QuartersIncomeJob;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new QuartersIncomeJob())->dailyAt('00:01');
Schedule::job(new EarningJob())->everyTenMinutes();
Schedule::job(new IsrcJob())->everyTenMinutes();
