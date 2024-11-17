<?php

use App\Jobs\CreateSendAnnouncement;
use App\Jobs\EarningJob;
use App\Jobs\IncomeReportJob;
use App\Jobs\QuartersIncomeJob;
use App\Jobs\SendAnnouncementJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// eğer database ve  tablo varsa çalışsın

//database var mı kontrol et

if (tenancy()->initialized) {
    if (Schema::hasTable('tenants') && Schema::hasTable('domains')) {
        \App\Models\System\Tenant::all()->each(function ($tenant) {
            tenancy()->initialize($tenant->id);
            Schedule::job(new CreateSendAnnouncement())->everyMinute();
            Schedule::job(new SendAnnouncementJob())->everyMinute();
            Schedule::job(new QuartersIncomeJob())->everyMinute();
            Schedule::job(new EarningJob())->everyMinute();
            Schedule::job(new IncomeReportJob())->everyMinute();
        });
    }
}

