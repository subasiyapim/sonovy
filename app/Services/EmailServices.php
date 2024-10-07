<?php

namespace App\Services;

use App\Mail\Announcement;
use App\Models\AnnouncementUser;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailServices
{
    public static function sendVerificationEmail(): void
    {

        Cache::forget('settings');

        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        config()->set('settings', $settings);



    }

    public static function sendAnnouncementEmail(AnnouncementUser $announcementUser): void
    {

        Cache::forget('settings');

        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        config()->set('settings', $settings);

        $email = new Announcement($settings, $announcementUser);

        try {

            Mail::to($announcementUser->user->email)->later(now()->addSeconds(5), $email);
            $announcementUser->status = 'DONE';
            $announcementUser->save();
        }
        catch (\Exception $e){

            Log::error($e->getMessage());
        }
    }

}
