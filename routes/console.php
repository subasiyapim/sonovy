<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use Illuminate\Support\Facades\Schedule;

//Schedule::job(new QuartersIncomeJob())->dailyAt('00:01');
//Schedule::job(new EarningJob())->dailyAt('00:001');
//Schedule::job(new IsrcJob())->everyMinute();
//Schedule::job(new MonthlyIncomeJob())->dailyAt('00:01');
