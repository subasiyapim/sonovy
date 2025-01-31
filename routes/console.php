<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use App\Models\System\Tenant;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new QuartersIncomeJob())->everyMinute();
Schedule::job(new EarningJob())->everyMinute();
Schedule::job(new IsrcJob())->everyMinute();
Schedule::job(new MonthlyIncomeJob())->everySixHours();
