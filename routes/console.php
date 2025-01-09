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
Schedule::job(new QuartersIncomeJob())->dailyAt('00:01');
Schedule::job(new EarningJob())->everyTenMinutes();
Schedule::job(new IsrcJob())->everyTenMinutes();
