<?php

use App\Jobs\EarningJob;
use App\Jobs\IsrcJob;

use App\Jobs\MonthlyIncomeJob;
use App\Jobs\QuartersIncomeJob;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessEarningReportJob;
use App\Models\EarningReportFile;
use App\Enums\EarningReportFileStatusEnum;

Schedule::job(new QuartersIncomeJob())->dailyAt('00:01');
Schedule::job(new EarningJob())->everyMinute()->withoutOverlapping();
Schedule::job(new IsrcJob())->everyMinute()->withoutOverlapping();
Schedule::job(new MonthlyIncomeJob())->dailyAt('00:01');
