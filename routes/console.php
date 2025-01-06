<?php

use App\Jobs\CreateSendAnnouncement;
use App\Jobs\EarningJob;
use App\Jobs\IncomeReportJob;
use App\Jobs\IsrcJob;
use App\Jobs\QuartersIncomeJob;
use App\Jobs\SendAnnouncementJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Schedule::job(new CreateSendAnnouncement())->everyMinute();
//Schedule::job(new SendAnnouncementJob())->everyMinute();
Schedule::job(new QuartersIncomeJob())->everyMinute();
Schedule::job(new EarningJob())->everyMinute();
//Schedule::job(new IncomeReportJob())->everyMinute();
Schedule::job(new IsrcJob())->everyTenMinutes();
